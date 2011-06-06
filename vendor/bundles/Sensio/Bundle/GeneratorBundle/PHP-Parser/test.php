<?php

function __autoload($class) {
    is_file($file = './lib/' . strtr($class, '_', '/') . '.php') && require_once $file;
}

echo '<pre>';

$parser = new Parser();
$parser->yydebug = false;

// Output Demo
$parser->yyparse(new Lexer(
    '<?php return array(1, \'foo\');'
    ),
    function($stmts) {
        foreach ($stmts as $stmt) {
            echo htmlspecialchars($stmt), "\n";
        }
    },
    function($msg) {
        echo $msg, "\n";
    }
);



/*
echo "\n\n";

// Correctness Demo
foreach (new RecursiveIteratorIterator(
             new RecursiveDirectoryIterator('.'),
             RecursiveIteratorIterator::LEAVES_ONLY)
         as $file) {
    if ('.php' !== substr($file, -4)) {
        continue;
    }

    set_time_limit(5);

    $startTime = microtime(true);
    $result = $parser->yyparse(
        new Lexer(file_get_contents($file)),
        function($stmts) { },
        function($msg) {
            echo $msg, "\n";
        }
    );
    $endTime = microtime(true);

    echo str_pad($file . ': ', 120, ' '), ($result == -1 ? 'successful' : 'ERROR'), ' (', $endTime - $startTime, ')', "\n";

    flush();
}*/
