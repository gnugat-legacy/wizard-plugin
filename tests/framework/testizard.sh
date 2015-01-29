#!/usr/bin/env bash

__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Custom configuration
for file in $__DIR__/../it_*.sh; do
    bash $__DIR__/set_up.sh
    echo $(echo $file | cut -d '/' -f 10 | cut -d '.' -f 1 | tr '_' ' ')
    bash $file
done
