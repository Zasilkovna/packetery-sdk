#!/bin/bash

docker run -it --rm -v $(pwd):/app packetery:apache-5.6 "$@"
