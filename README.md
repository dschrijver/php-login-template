# php-login-template

A simple php login template. 

Create the `users` table with the following SQL query:

``` sql
CREATE TABLE `users` (
    `id` int NOT NULL AUTO_INCREMENT,
    `username` varchar(20) NOT NULL,
    `password` char(60) NOT NULL,
    `email` varchar(255) NOT NULL,
    `email_confirmed` bit(1) NOT NULL,
    `activation_code` char(60) NOT NULL,
    PRIMARY KEY (`id`)
)
```
