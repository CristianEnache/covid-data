#!/bin/bash

#cd /var/www/html/storage/app/private/covid-19-data
#git pull


#Get data from: https://github.com/owid/covid-19-data
cd /var/www/html/storage/app/private/owid_covid-19-data

#remove existing files
rm -f owid-covid-data.json
rm -f vaccinations.json

# get new files
wget https://raw.githubusercontent.com/owid/covid-19-data/master/public/data/owid-covid-data.json -P /var/www/html/storage/app/private/owid_covid-19-data
wget https://raw.githubusercontent.com/owid/covid-19-data/master/public/data/vaccinations/vaccinations.json -P /var/www/html/storage/app/private/owid_covid-19-data


# Get other data
cd /var/www/html/storage/app/private/other_data
rm -f OxCGRT_vaccines_full.csv
wget https://raw.githubusercontent.com/OxCGRT/covid-policy-tracker/master/data/OxCGRT_vaccines_full.csv -P /var/www/html/storage/app/private/other_data

