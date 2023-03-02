sudo su
yum update -y

//install repositories
amazon-linux-extras install lamp-mariadb10.2-php7.2 php7.2 -y

//instlal extention
yum install php php-cli php-mysqlnd php-pdo php-common php-fpm -y
yum install php-gd php-mbstring php-xml php-dom php-intl php-simplexml -y
yum install -y nginx1
yum install -y mariadb-server

systemctl start nginx
systemctl enable nginx
systemctl start php-fpm
systemctl enable php-fpm

systemctl start mariadb

//change password user mysql
mysql -u root -p < .query.sql


mkdir /var/www/
mv ../tool /var/www/
mv ../wordpress /var/www/
mkdir /var/www/wordpress/wp-content/uploads
chmod -R 777 /var/www/wordpress/wp-content/uploads

cp tool.conf /etc/nginx/conf.d/
cp wordpress.conf /etc/nginx/conf.d/

systemctl restart nginx

//setup tool
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash
. ~/.nvm/nvm.sh
nvm install 17
npm install pm2 -g

cd /var/www/tool
npm i
npm run build
pm2 start /var/www/tool/.output/server/index.mjs






