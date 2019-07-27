<?php

if ( $argc < 3 ) {
    die( "\nUSAGE: php ad-generator-cli.php -n 300 -f shablon.txt -o result.txt\n\n" );
}

function read_file ( $filename ) {
    $fp = fopen( $filename, "r" ) or die( "\nread_file: Unable to open file!\n\n" );
    $content = fread( $fp, filesize( $filename ) );
    fclose( $fp );
    return $content;
}

function save_file ( $filename, $content ) {
    $fp = fopen( $filename, "w" ) or die( "\nsave_file: Unable to open file!\n\n" );
    fwrite( $fp, $content );
    fclose( $fp );
}

$N = -1;
$file_in = '';
$file_out = '';

for( $i = 1; $i < $argc; $i++ )
{
    if ( ( !strcmp( $argv[$i], "-n" ) || !strcmp( $argv[$i], "-N" ) ) && ( $i+1<$argc ) ) {
        $N = ( int )$argv[$i+1];
    }
    if ( ( !strcmp( $argv[$i], "-f" ) || !strcmp( $argv[$i], "--file" ) ) && ( $i+1<$argc ) ) {
        $file_in = ( string )$argv[$i+1];
    }
    if ( ( !strcmp( $argv[$i], "-o" ) || !strcmp( $argv[$i], "--out" ) ) && ( $i+1<$argc ) ) {
        $file_out = ( string )$argv[$i+1]; 
    }
}

if ( $N < 1 ) {
    echo "\nWrong n! Using default n = 300\n\n";
    $N = 300;
}
if ( $file_in == '' ) {
    die( "\nUSAGE: php ad-generator-cli.php -n 300 -f shablon.txt -o result.txt\n\n" );
}
if ( $file_out == '' ) {
    $file_out = 'result-' . $N . '.txt';
}

save_file( $file_out, ad_generator_cli( $N, $file_in ) );

function ad_generator_cli( $max_res, $filename ) {
    $result_text = '';
    $ad_text = read_file( $filename );
    $ad_text = str_replace( '\\\\', '\\', $ad_text );
    $ad_text = str_replace( '\\"', '"', $ad_text );
    $ad_text = str_replace( "\\'", "'", $ad_text );

    if ( $ad_text ) {
        require_once 'includes/Natty/TextRandomizer.php';
        $tRand = new Natty_TextRandomizer( $ad_text );
        $num_var = $tRand->numVariant();
        
        if ( $num_var > 1 ) {
            $max_tmp = min( $num_var, $max_res );
            $result_text .= sprintf( "The number of all possible variants: %s. Here are the random %s of them:\n\n", $num_var, $max_tmp );
            
            for ( $i = 0; $i < $max_tmp; ++$i ) {
                $result_text .= $tRand->getText() . "\n\n\n";
            }
        } else {
            $result_text .= "Only 1 possible variant:\n\n";
            $result_text .= $tRand->getText();
        }
    }
    $result_text = preg_replace( "/\n /", "\n", trim( $result_text ) );
    $result_text = preg_replace( "/ \n/", "\n", $result_text );
    return $result_text;
}

