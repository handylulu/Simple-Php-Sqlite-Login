Handy Php/Sqlite login form
=============================

_By Lucas Zhuang_

● Description
----------------------------
This is a simple Php/Login form. No registration provided. You can insert login information to Sqlite database directly with SHA512.

● Features
----------------------------
1. simple. Just two php files with a users.db sqlite3 database.
2. users.db contains two tables: "users" and "active_users". 
3. Hashing is done with SHA512. Sessions are hashed with salt and http_user_agent. password is hashed with salt and text password.
4. you put the following code to whatever php to present contents that need content authentification.
		require("user_config.php");
		if(isLoggedIn()){
		$user=isLoggedIn();
		updateExpire($user['id']);
		}
		else{
			header('location:user.php');
		}
5. to create a new user, simply insert a pair of email address and hashed password.
Tip: to hash a password in MacOS: 
		echo -n password-of-choice | openssl sha512

● SQL to create the two tables
----------------------------

		DROP TABLE IF EXISTS "active_users";
		CREATE TABLE "active_users" (
			 "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
			 "user_id" integer(6,0) NOT NULL DEFAULT 1,
			 "session_id" text(256,0),
			 "hash" integer(256,0),
			 "expires" integer(64,0),
			CONSTRAINT "user_id" FOREIGN KEY ("user_id") REFERENCES "users" ("ID") ON DELETE CASCADE ON UPDATE CASCADE
		);
		INSERT INTO "main".sqlite_sequence (name, seq) VALUES ("active_users", '41');

		DROP TABLE IF EXISTS "users";
		CREATE TABLE "users" (
			 "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
			 "user_name" text(128,0),
			 "name" text(128,0) NOT NULL,
			 "email" text(128,0) NOT NULL,
			 "password" text(256,0),
			 "user_type" integer(2,0) NOT NULL DEFAULT 1
		);
		INSERT INTO "main".sqlite_sequence (name, seq) VALUES ("users", '1');

