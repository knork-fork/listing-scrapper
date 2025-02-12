#!/bin/bash

while true  
do

echo "$(tput setaf 6)Pinging Discord to prove server is alive:$(tput sgr 0)"
php ping.php

#echo "$(tput setaf 6)Executing scrapper script:$(tput sgr 0)"
#php run.php

# loop every 60 minutes
sleep 3600 
done
