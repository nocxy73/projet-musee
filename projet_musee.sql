
use projet_musee;
create table exposition (
id_exposition int primary key not null,
descriptif varchar(1000),
libelle varchar(50)
);

create table visiteur (
id_visiteur int  unsigned primary key not null auto_increment,
nom varchar(15),
prenom varchar(15),
age int,
tel int,
mail varchar(50),
h_arrivee datetime,
h_depart datetime
);

create table type_tickets (
id_ticket int  unsigned not null  primary key,
libelle varchar(50)
);

create table visite (
id_visiteur int,
id_exposition int,
constraint fk_visiteur foreign key (id_visiteur) references visiteur (id_visiteur),
constraint fk_exposition foreign key (id_exposition) references exposition (id_exposition),
constraint fk_ticket foreign key (id_ticket) references tickets (id_ticket)
);


