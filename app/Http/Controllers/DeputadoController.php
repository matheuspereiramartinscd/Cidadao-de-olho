<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Deputado;
use App\Models\RedeSocial;
use Carbon\Carbon;

class DeputadoController extends Controller
{
    // Método para carregar deputados da API e salvar no banco
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

                // Salvar ou atualizar o deputado
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
                
                // Salvar as redes sociais, se existirem
                if (isset($deputado['redesSociais'])) {
                    foreach ($deputado['redesSociais'] as $redeSocial) {
                        RedeSocial::updateOrCreate(
                            [
                                'deputado_id' => $savedDeputado->id,
                                'nome' => $redeSocial['redeSocial']['nome']
                            ],
                            [
                                'url' => $redeSocial['url']
                            ]
                        );
                    }
                }
            }

            return response()->json($deputados);
        }

        return response()->json(['error' => 'Erro ao consultar a API'], 500);
    }

    // Método para calcular e retornar o ranking dos reembolsos de Janeiro
    // Método para calcular e retornar o ranking dos reembolsos de Janeiro
    // Método para calcular e retornar o ranking dos reembolsos de Fevereiro
    
// Método para retornar o total de reembolsos de um único deputado (ID 4458)
public function totalReembolsadoDeputado4458()
{
    $deputadoId = 4458; // ID do deputado
    $ano = 2019;
    $mes = 2;  // Fevereiro

    // URL da API para reembolsos
    $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputadoId}/{$ano}/{$mes}?formato=json";
    
    // Realizando a requisição
    $response = Http::get($url);

    // Verificando se a requisição foi bem-sucedida
    if ($response->successful()) {
        $dados = $response->json(); // Pegando os dados da resposta

        // Adicionando log para verificar o que foi retornado
        \Log::info('Dados da API:', $dados);

        $totalReembolsado = 0;

        // Iterando por cada tipo de despesa (em "list")
        foreach ($dados['list'] as $item) {
            // Verificando se 'listaDetalheVerba' está presente
            if (isset($item['listaDetalheVerba'])) {
                // Iterando pelos detalhes da verba
                foreach ($item['listaDetalheVerba'] as $detalhe) {
                    // Somando o valor reembolsado (garantindo que seja um número inteiro)
                    $totalReembolsado += (float) $detalhe['valorReembolsado'];
                }
            }
        }

        // Formatando o total reembolsado para o formato monetário
        $totalReembolsadoFormatted = number_format($totalReembolsado, 2, ',', '.');

        // Retornando o resultado como JSON
        return response()->json([
            'deputado_id' => $deputadoId,
            'total_reembolsado' => "R$ {$totalReembolsadoFormatted}"
        ]);
    }

    // Caso haja erro na requisição, retornamos um erro
    return response()->json(['error' => 'Erro ao consultar a API de reembolsos'], 500);
}
public function rankingReembolsosJaneiro()
{
    $mes = 1;  // Janeiro
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}

// Método para calcular e retornar o ranking dos reembolsos de Fevereiro
// Método para calcular e retornar o ranking dos reembolsos de Fevereiro
// Método para calcular e retornar o ranking dos reembolsos de Fevereiro
public function rankingReembolsosFevereiro()
{
    $mes = 2;  // Fevereiro
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}

public function rankingReembolsosMarco ()
{
    $mes = 3;  // Marco 
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}

public function rankingReembolsosAbril ()
{
    $mes = 4;  // Abril  
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}

public function rankingReembolsosMaio ()
{
    $mes = 5;  // Maio  
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}

public function rankingReembolsosJunho ()
{
    $mes = 6;  // Junho  
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}

public function rankingReembolsosJulho ()
{
    $mes = 7;  // Julho  
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}

public function rankingReembolsosAgosto ()
{
    $mes = 8;  // Agosto  
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}


public function rankingReembolsosSetembro ()
{
    $mes = 9;  // Setembro  
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}


public function rankingReembolsosOutubro ()
{
    $mes = 10;  // Outubro  
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}

public function rankingReembolsosNovembro ()
{
    $mes = 11;  // Novembro  
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}

public function rankingReembolsosDezembro ()
{
    $mes = 12;  // Dezembro  
    $ano = 2019;

    // Log para iniciar o processo
    \Log::info("Iniciando consulta de reembolsos para o mês de Fevereiro {$ano}");

    $deputadosReembolsos = Deputado::all()->map(function ($deputado) use ($mes, $ano) {
        $totalReembolsado = 0;

        // Consumindo a API de reembolsos para o mês de Fevereiro para cada deputado
        $url = "https://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/deputados/{$deputado->id}/{$ano}/{$mes}?formato=json";
        
        // Log para verificar a URL da API para cada deputado
        \Log::info("Consultando reembolsos do deputado ID: {$deputado->id} - URL: {$url}");
        
        $response = Http::get($url);

        // Verificando se a requisição foi bem-sucedida
        if ($response->successful()) {
            $reembolsos = $response->json()['list'];

            // Log da resposta da API para o deputado específico
            \Log::info("Resposta da API para o deputado ID {$deputado->id}:", $reembolsos);

            // Iterar por cada item de reembolso na lista
            foreach ($reembolsos as $reembolso) {
                // Verificando se a chave 'listaDetalheVerba' existe
                if (isset($reembolso['listaDetalheVerba'])) {
                    foreach ($reembolso['listaDetalheVerba'] as $detalhe) {
                        // Log para verificar o valor de cada detalhe de reembolso
                        \Log::info("Detalhe de reembolso do deputado ID {$deputado->id}: " . json_encode($detalhe));

                        $valorReembolsado = isset($detalhe['valorReembolsado']) ? (float) $detalhe['valorReembolsado'] : 0;

                        // Log para verificar o valor reembolsado de cada detalhe
                        \Log::info("Valor reembolsado para o deputado ID {$deputado->id}: R$ " . number_format($valorReembolsado, 2, ',', '.'));

                        $totalReembolsado += $valorReembolsado; // Acumulando o valor
                    }
                }
            }
        } else {
            // Caso a requisição falhe, logar o erro
            \Log::error("Erro ao consultar reembolsos do deputado ID {$deputado->id}. Resposta da API: " . $response->body());
        }

        // Log do total de reembolso calculado para o deputado
        \Log::info("Total reembolsado pelo deputado ID {$deputado->id}: R$ " . number_format($totalReembolsado, 2, ',', '.'));

        // Retorna o deputado com o total de reembolsos
        return [
            'deputado' => $deputado,
            'total_reembolsado' => $totalReembolsado,
        ];
    });

    // Ordena os deputados pelo total de reembolsos de forma decrescente
    $topDeputados = $deputadosReembolsos->sortByDesc('total_reembolsado')->take(5);

    // Log dos 5 deputados com maiores reembolsos
    \Log::info("Top 5 deputados com maiores reembolsos de Fevereiro {$ano}:", $topDeputados->toArray());

    // Formatar os valores de reembolso para exibição
    $topDeputadosFormatted = $topDeputados->map(function ($deputadoReembolso) {
        $deputadoReembolso['total_reembolsado'] = "R$ " . number_format($deputadoReembolso['total_reembolsado'], 2, ',', '.');
        return $deputadoReembolso;
    });

    // Retorna o resultado em formato JSON com os 5 deputados com maiores valores reembolsados
    return response()->json($topDeputadosFormatted);
}


    // Método para exibir o ranking das redes sociais
    public function rankingRedesSociais()
    {
        // Consulta para contar o número de deputados em cada rede social
        $redesSociais = RedeSocial::join('deputados', 'rede_sociais.deputado_id', '=', 'deputados.id')
            ->groupBy('rede_sociais.nome') // Agrupar pelo nome da rede social
            ->selectRaw('count(rede_sociais.deputado_id) as total, rede_sociais.nome')
            ->orderByDesc('total') // Ordena de forma decrescente pelo número de deputados
            ->get();

        return response()->json($redesSociais);
    }
}
