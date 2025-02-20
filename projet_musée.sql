use projet_musée;
create table exposition (
id_exposition int  unsigned primary key not null auto_increment


);

create table visiteur (
id_visiteur int  unsigned primary key not null auto_increment,
nom varchar(15),
prenom varchar(15),
age int,
tel int,
mail varchar(50)
);

create table tickets (
id_ticket int primary key not null,
num_ticket int  unsigned not null auto_increment
);
