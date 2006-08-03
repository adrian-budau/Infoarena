// Automated test for jrun.
// The following are parsed by test.php
// JRUN_ARGS = --time-limit 1000 --memory-limit 1500 
// JRUN_RES = OK: time [0-9]+ms memory [0-9]{4,}kb: Execution successful.
#include <stdio.h>
#include <string.h>
#include <stdlib.h>

int main(void)
{
    int used = 0;
    int q;
    while (used < 1000000) {
        q = 50;
        used += q;
        malloc(q);
    }
    return 0;
}
