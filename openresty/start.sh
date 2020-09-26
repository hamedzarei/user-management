#!/usr/bin/env bash

cd openresty
docker-compose up -d
cd ..


arr=("auth-controller" "python-mm")

for i in "${arr[@]}"
do
   cd $i
   docker-compose up -d
   cd ..
done