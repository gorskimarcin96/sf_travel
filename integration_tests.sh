rm -R var/log/*.log
docker-compose -f docker/docker-compose.yml exec backend ./bin/console doctrine:database:drop --force -n -q
docker-compose -f docker/docker-compose.yml exec backend ./bin/console doctrine:database:create -n -q
docker-compose -f docker/docker-compose.yml exec backend ./bin/console doctrine:migration:migrate -n -q

echo "Test for download \"Zakynthos\":"
docker-compose -f docker/docker-compose.yml exec backend php -d memory_limit=-1 ./bin/console app:integration-tests:search grecja zakynthos WAW ZTH

echo "Test for download \"Rodos\":"
docker-compose -f docker/docker-compose.yml exec backend php -d memory_limit=-1 ./bin/console app:integration-tests:search grecja rodos WAW KGS

echo "Test for download \"Kreta\":"
docker-compose -f docker/docker-compose.yml exec backend php -d memory_limit=-1 ./bin/console app:integration-tests:search grecja kreta WAW CHQ

echo "Test for download \"Majorka\":"
docker-compose -f docker/docker-compose.yml exec backend php -d memory_limit=-1 ./bin/console app:integration-tests:search hiszpania majorka WAW PMI

echo "Test for download \"Ibiza\":"
docker-compose -f docker/docker-compose.yml exec backend php -d memory_limit=-1 ./bin/console app:integration-tests:search hiszpania ibiza WAW IBZ

echo "Test for download \"Alanya\":"
docker-compose -f docker/docker-compose.yml exec backend php -d memory_limit=-1 ./bin/console app:integration-tests:search turcja alanya WAW AYT

echo "Test for download \"Bodrum\":"
docker-compose -f docker/docker-compose.yml exec backend php -d memory_limit=-1 ./bin/console app:integration-tests:search turcja bodrum WAW BJV

echo "Test for download \"Hurghada\":"
docker-compose -f docker/docker-compose.yml exec backend php -d memory_limit=-1 ./bin/console app:integration-tests:search egipt hurghada WAW HRG

echo "Test for download \"Ayia Napa\":"
docker-compose -f docker/docker-compose.yml exec backend php -d memory_limit=-1 ./bin/console app:integration-tests:search egipt "ayia napa" WAW LCA
