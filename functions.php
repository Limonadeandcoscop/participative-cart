<?php

/**
 * Truncates the given string at the specified length.
 *
 * @param string $str The input string.
 * @param int $width The number of chars at which the string will be truncated.
 * @return string
 */
function cut_string($str, $width = 250) {
    return strtok(wordwrap($str, $width, "...\n"), "\n");
}