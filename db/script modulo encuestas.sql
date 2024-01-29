create table Enc_Pregunta(
	ID_pregunta int primary key identity(1,1),
	pregunta varchar(255) not null,
	fecha datetime not null,
	status tinyint not null default 1
);

create table Enc_Encuesta(
	ID_encuesta int primary key identity(1,1),
	nombre varchar(255) not null,
	fecha datetime not null,
	status tinyint not null default 1
);

create table Enc_Universo(
	ID_universo int primary key identity(1,1),
	ID_encuesta int not null,
	ID_pregunta int not null,

	constraint ENC_UNIVERSO_FK_ENCUESTA foreign key(ID_encuesta) references Enc_Encuesta(ID_encuesta),
	constraint ENC_UNIVERSO_FK_PREGUNTA foreign key(ID_pregunta) references Enc_Pregunta(ID_pregunta)
);

create table Enc_Url(
	ID_url int primary key identity(1,1),
	ID_encuesta int not null,
	num_preguntas int not null,
	fecha datetime not null,
	status tinyint not null default 1,

	constraint ENC_URI_FK_ENCUESTA foreign key(ID_encuesta) references Enc_Encuesta(ID_encuesta)
);

create table Enc_Intento(
	ID_intento int primary key identity(1,1),
	ID_url int not null,
	nombre varchar(255) not null,
	correo varchar(255) not null,
	inicio datetime not null,
	final datetime not null,
	empresa varchar(255) not null,
	area varchar(255) not null,
	cargo varchar(255) not null,
	telefono varchar(255) not null,

	constraint ENC_INTENTO_FK_URL foreign key(ID_url) references Enc_Url(ID_url)
);

create table Enc_Respuesta(
	ID_respuesta int primary key identity(1,1),
	ID_intento int not null,
	ID_pregunta int not null,
	puntaje int not null,

	constraint ENC_RESPUESTA_FK_INTENTO foreign key(ID_intento) references Enc_Intento(ID_intento),
	constraint ENC_RESPUESTA_FK_PREGUNTA foreign key(ID_pregunta) references Enc_Pregunta(ID_pregunta)
);