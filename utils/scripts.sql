drop schema if exists PhpA6;
create schema PhpA6;
use PhpA6;

drop table if exists Customers;
drop table if exists Stores;

create table Stores
(
    storeCode int,
    storeAddress varchar(80) not null,
    phone varchar(15) not null,
    primary key (storeCode)
);


create table Customers
(
    customerID int auto_increment,
    firstName varchar(80) ,
    lastName varchar(80) ,
    dateOfBirth date ,
    phone varchar(10),
    email varchar(80),	
    loyaltyMember boolean,
    favoriteStore int not null,
    primary key (customerID),
    foreign key (favoriteStore) references Stores(storeCode)
);


-- Stores
insert into Stores values (201, '33 Regent St', '5065884728');
insert into Stores values (202, '25 Rosemary Ave', '5065884728');
insert into Stores values (203, '122 Boulevard St', '5065668659');


-- Customers
insert into Customers values(null, 'Gregory', 'Baptiste', '1988-06-06' , '5065668923', 'greg123@gmail.com', true, 201);
insert into Customers values(null, 'Stacy', 'Parker', '1976-07-15' , '5065661234', 'stacy123@hotmail.com', true, 202);
insert into Customers values(null, 'Martin', 'Lauren', '1956-12-13' , '5065665432', 'martin123@outlook.com', false, 202);
insert into Customers values(null, 'Bob', 'Chilton', '1982-10-09' , '5065665623', 'bob123@gmail.com', true, 201);
insert into Customers values(null, 'Jane', 'Doe', '1979-04-06' , '5065660954', 'jane123@gmail.com', false, 203);
insert into Customers values(null, 'Jerry', 'Mice', '1981-06-27' , '5065664365', 'jery123@gmail.com', false, 201);
insert into Customers values(null, 'Harold', 'Reedy', '1997-12-08' , '5065664397', 'harold123@gmail.com', false, 202);
insert into Customers values(null, 'Virginia', 'Campa', '1983-12-03' , '5065666289', 'virginia123@gmail.com', false, 203);
insert into Customers values(null, 'Stuart', 'Bower', '1988-09-12' , '5065663569', 'stuart123@outlook.com', true, 201);
insert into Customers values(null, 'Mike', 'Willian', '1967-08-08' , '5065662267', 'mike123@outlook.com', true, 201);
insert into Customers values(null, 'Fernanda', 'Silva', '1981-07-01' , '5065666744', 'fer123@hotmail.com', false, 203);
insert into Customers values(null, 'Valerie', 'Wilson', '1944-08-28' , '5065666685', 'val123@outlook.com', true, 203);
insert into Customers values(null, 'Julia', 'Martins', '1998-02-01' , '5065662694', 'julia123@hotmail.com', false, 202);
insert into Customers values(null, 'Stephan', 'Larosse', '1982-12-30' , '5065667586', 'stephan123@gmail.com', true, 201);
insert into Customers values(null, 'Maria', 'Menezes', '1977-09-02' , '5065660668', 'maria123@gmail.com', true, 202);
insert into Customers values(null, 'Judy', 'Jones', '1979-12-14' , '5065661447', 'judy123@outlook.com', true, 203);
insert into Customers values(null, 'John', 'Chilton', '1998-04-13' , '5065664264', 'john123@hotmail.com', false, 201);
insert into Customers values(null, 'Gabriela', 'Santos', '1996-03-24' , '5065668846', 'gabriela123@hotmail.com', false, 203);
insert into Customers values(null, 'Onofre', 'Lopes', '2000-02-20' , '5065664466', 'onofre123@gmail.com', true, 201);
insert into Customers values(null, 'Sheyla', 'Farkell', '1981-12-16' , '5065663333', 'sheyla123@gmail.com', false, 201);

