<?php
// admin.php - Painel para visualizar uploads da prova

$pasta_uploads = 'uploads_prova/';
$arquivo_log = $pasta_uploads . 'log_uploads.txt';

// Função para listar arquivos
function listarArquivos($pasta) {
    $arquivos = array();
    if (is_dir($pasta)) {
        $files = scandir($pasta);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'pkt') {
                $arquivos[] = array(
                    'nome' => $file,
                    'tamanho' => filesize($pasta . $file),
                    'data' => filemtime($pasta . $file)
                );
            }
        }
        // Ordena por data (mais recente primeiro)
        usort($arquivos, function($a, $b) {
            return $b['data'] - $a['data'];
        });
    }
    return $arquivos;
}

// Função para ler o log
function lerLog($arquivo) {
    $logs = array();
    if (file_exists($arquivo)) {
        $linhas = file($arquivo, FILE_IGNORE_NEW_LINES);
        $logs = array_reverse($linhas); // Mais recente primeiro
    }
    return $logs;
}

$arquivos = listarArquivos($pasta_uploads);
$logs = lerLog($arquivo_log);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Arquivos da Prova</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .stats {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .stats h2 {
            margin: 0;
            color: #1976d2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .download-btn {
            background-color: #4caf50;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
        }
        .log-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }
        .log-item {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-family: monospace;
            font-size: 14px;
        }
        .refresh-btn {
            background-color: #2196f3;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            float: right;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Painel - Arquivos da Prova de Rede</h1>
        
        <a href="?" class="refresh-btn">🔄 Atualizar</a>
        <div style="clear: both;"></div>
        
        <div class="stats">
            <h2><?php echo count($arquivos); ?> arquivo(s) recebido(s)</h2>
            <p>Total de submissões da prova</p>
        </div>

        <h2>📁 Arquivos Recebidos</h2>
        <?php if (count($arquivos) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>👤 Aluno</th>
                    <th>📄 Arquivo</th>
                    <th>📅 Data/Hora</th>
                    <th>💾 Tamanho</th>
                    <th>⬇️ Download</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arquivos as $arquivo): ?>
                <tr>
                    <td>
                        <?php 
                        // Extrai o nome do aluno do nome do arquivo
                        $nome_parts = explode('_', $arquivo['nome']);
                        echo htmlspecialchars(str_replace('_', ' ', $nome_parts[0]));
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($arquivo['nome']); ?></td>
                    <td><?php echo date('d/m/Y H:i:s', $arquivo['data']); ?></td>
                    <td><?php echo round($arquivo['tamanho'] / 1024, 2); ?> KB</td>
                    <td>
                        <a href="<?php echo $pasta_uploads . $arquivo['nome']; ?>" 
                           class="download-btn" download>📥 Baixar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="text-align: center; color: #666; padding: 40px;">
            📭 Nenhum arquivo recebido ainda
        </p>
        <?php endif; ?>

        <div class="log-section">
            <h3>📋 Log de Uploads</h3>
            <?php if (count($logs) > 0): ?>
                <?php foreach (array_slice($logs, 0, 20) as $log): // Mostra últimos 20 ?>
                <div class="log-item"><?php echo htmlspecialchars($log); ?></div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: #666;">Nenhum upload registrado ainda</p>
            <?php endif; ?>
        </div>

        <div style="text-align: center; margin-top: 30px; color: #666;">
            <p>🔄 Esta página atualiza automaticamente. Última atualização: <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>
</body>
</html>
