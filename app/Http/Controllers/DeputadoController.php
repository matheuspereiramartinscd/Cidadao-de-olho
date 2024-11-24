<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deputado;
use App\Models\RedeSocial;
use Carbon\Carbon;

class DeputadoController extends Controller
{


    /**
     * Carrega a lista de deputados da API externa e salva os dados no banco de dados.
     * Também salva as redes sociais associadas aos deputados.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDeputados()
    {
        $response = Http::get('https://dadosabertos.almg.gov.br/ws/deputados/lista_telefonica?formato=json');

        if ($response->successful()) {
            $deputados = $response->json()['list'];

            foreach ($deputados as $deputado) {
                $dataNascimento = isset($deputado['dataNascimento'])
                    ? Carbon::createFromFormat('d/m/Y', $deputado['dataNascimento'])->format('Y-m-d')
                    : null;

                $sitePessoal = $deputado['sitePessoal'] ?? null;
                $atividadeProfissional = $deputado['atividadeProfissional'] ?? null;

                $savedDeputado = Deputado::updateOrCreate(
                    ['id' => $deputado['id']],
                    [
                        'nome' => $deputado['nome'],
                        'nomeServidor' => $deputado['nomeServidor'],
                        'partido' => $deputado['partido'],
                        'endereco' => $deputado['endereco'],
                        'telefone' => $deputado['telefone'],
                        'fax' => $deputado['fax'],
                        'email' => $deputado['email'],
                        'sitePessoal' => $sitePessoal,
                        'atividadeProfissional' => $atividadeProfissional,
                        'naturalidadeMunicipio' => $deputado['naturalidadeMunicipio'],
                        'naturalidadeUf' => $deputado['naturalidadeUf'],
                        'dataNascimento' => $dataNascimento,
                    ]
                );

                if (isset($deputado['redesSociais'])) {
                    foreach ($deputado['redesSociais'] as $redeSocial) {
                        RedeSocial::updateOrCreate(
                            [
                                'deputado_id' => $savedDeputado->id,
                                'nome' => $redeSocial['redeSocial']['nome'],
                            ],
                            [
                                'url' => $redeSocial['url'],
                            ]
                        );
                    }
                }
            }

            return response()->json($deputados);
        }

        return response()->json(['error' => 'Erro ao consultar a API'], 500);
    }




    /**
     * Retorna a lista de todos os deputados salvos no banco de dados.
     *
     * @return \Illuminate\View\View
     */
    public function deputados()
    {
        $deputados = Deputado::all();
        return view('deputados', compact('deputados'));
    }




    /**
     * Retorna o ranking dos 5 deputados com maiores reembolsos em um mês específico.
     *
     * @param int $mes
     * @return \Illuminate\Http\JsonResponse
     */
    public function rankingReembolsos($mes)
    {
        $ano = 2019;

        if ($mes < 1 || $mes > 12) {
            return response()->json(['error' => 'Mês inválido. Insira um valor entre 1 e 12.'], 400);
        }

        $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
            $totalReembolsado = 0;

            $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";

            $response = Http::get($url);

            if ($response->successful()) {
                $reembolsos = $response->json()['list'] ?? [];

                foreach ($reembolsos as $reembolso) {
                    if (isset($reembolso['listaDetalheVerba'])) {
                        foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                            $valorReembolsado = (float) ($detalhe['valorReembolsado'] ?? 0);
                            $totalReembolsado += $valorReembolsado;
                        }
                    }
                }
            }

            return [
                'deputado' => $deputado,
                'total_reembolsado' => $totalReembolsado,
            ];
        });

        $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

        $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
            $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
            return $deputadoReembolso;
        });

        return response()->json($topDeputadosFormatted);
    }




    /**
     * Retorna a view do ranking de reembolsos.
     */
    public function rankingReembolsosView()
    {
        return view('ranking-reembolsos');
    }




    
    /**
     * Calcula o ranking das redes sociais mais utilizadas pelos deputados.
     *
     * @param bool $returnJson Indica se o resultado deve ser retornado como JSON.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View JSON ou View dependendo do uso.
     */
    public function rankingRedesSociais($returnJson = true)
    {
        $redesSociais = RedeSocial::join('deputados', 'rede_sociais.deputado_id', '=', 'deputados.id')
            ->groupBy('rede_sociais.nome')
            ->selectRaw('count(rede_sociais.deputado_id) as total, rede_sociais.nome')
            ->orderByDesc('total')
            ->get();

        if ($returnJson) {
            return response()->json($redesSociais);
        }

        return view('ranking-redes-sociais', ['redesSociais' => $redesSociais]);
    }
}
