#!/bin/bash

#cd /var/www/html/storage/app/private/covid-19-data
#git pull


#Get data from: https://github.com/owid/covid-19-data
cd /var/www/html/storage/app/private/owid_covid-19-data

#remove existing files
rm -f owid-covid-data.json
rm -f vaccinations.json
rm -f owid-covid-latest.json

# get new files
wget https://raw.githubusercontent.com/owid/covid-19-data/master/public/data/owid-covid-data.json -P /var/www/html/storage/app/private/owid_covid-19-data
wget https://raw.githubusercontent.com/owid/covid-19-data/master/public/data/vaccinations/vaccinations.json -P /var/www/html/storage/app/private/owid_covid-19-data
wget https://raw.githubusercontent.com/owid/covid-19-data/master/public/data/latest/owid-covid-latest.json -P /var/www/html/storage/app/private/owid_covid-19-data


# Get other data
cd /var/www/html/storage/app/private/other_data

rm -f OxCGRT_latest.csv
rm -f Region_Mobility_Report_CSVs.zip
wget https://raw.githubusercontent.com/OxCGRT/covid-policy-tracker/master/data/OxCGRT_latest.csv -P /var/www/html/storage/app/private/other_data
wget https://www.gstatic.com/covid19/mobility/Region_Mobility_Report_CSVs.zip -P /var/www/html/storage/app/private/other_data
unzip -o /var/www/html/storage/app/private/other_data/Region_Mobility_Report_CSVs.zip -d /var/www/html/storage/app/private/other_data/mobility_data
chown -R www-data:www-data /var/www/html/storage/app/private/other_data/mobility_data

#Dispatch jobs
cd /var/www/html
php artisan csvs:process
