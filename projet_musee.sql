
use projet_musee;
create table exposition (
num_exposition int  unsigned primary key not null auto_increment,
id_exposition int
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
num_ticket int unsigned primary key not null auto_increment,
id_ticket int  unsigned not null
);

create table visite (
id_visite int  unsigned primary key not null auto_increment,
id_visiteur int,
id_exposition int,
id_ticket int,
h_arrivee datetime,
h_depart datetime,
constraint fk_visiteur foreign key (id_visiteur) references visiteur (id_visiteur),
constraint fk_exposition foreign key (id_exposition) references exposition (id_exposition),
constraint fk_ticket foreign key (id_ticket) references tickets (id_ticket)
);


