# esbart

esbart is an LDAP web frontend written in PHP.

## Description

esbart allows any organisation to easily manage an LDAP directory. It does not handle transactions using LDIF files, instead, it provides an oriented interface based on the *domain controller* functionalities related to user accounts, their credentials, memberships, etc.

The software will also deliver one-time password change tokens via email to streamline unattended credential management, and also instructional emails to set-up and welcome users.

## Getting Started

### Dependencies

* An LDAP directory (OpenLDAP for instance)
* A webserver
* A MySQL/MariaDB database
* A Mail Transmission Agent
* PHP modules
	* php-mbstring
	* php-ldap
	
The database allows storing password change keys and expire them accordingly, and the MTA dispatches the emails sent by the system.

### Installing

For now, there is no automated script to install the software.

#### MariaDB

The single-table database can be created pushing `esbart.sql` to MariaDB

```
mysql < esbart.sql
```

The configuration keys in regards of the database connection can be found in `config.php`, MariaDB section.

#### cron

The unused password change keys *can* be expired regularly using cron. For instance, daily:

```
sudo crontab -u apache -l
0 * * * * /bin/php -f /path/to/cron.php
```

Or manually

```
sudo -u apache /bin/php -f /path/to/cron.php
```

### Configuration

The so-called `config.php` file also allows configuring as per the below settings, amongst many others:

| Setting | Description |
| - | - |
| `MODE` | Switches from *light* to *dark* CSS style |
| `LOGGING` | Activates a pair of access logfiles under *log/* |
| `LDAP_AUTH_GROUP` | Establishes the group of users that can access esbart |
| `LDAP_GROUP_EXCLUSIONS` | Establishes the groups that are **not manageable** in esbart |
| `LDAP_SAMBA_SID` | Sets the Samba domain SID prefix |

The software can be easily localised with language files under *locale/*.

esbart will look after the Samba credentials as well, `sambaNTpassword`, which will be synced to the main password `userPassword`.

## Screenshots

![User list](/screenshots/list-user.png?raw=true "User list")
![Create user - assisted mode](/screenshots/add-user-assisted.png?raw=true "Create user - assisted mode")

The previous screenshot shows a simplified form (called *assisted mode*) that is intended for user creation by those not used to IT procedures. It sorts out the user name automatically matching an specific format and dispatches the password creation email request on the fly.

![Edit user](/screenshots/edit.png?raw=true "Edit user")
![Group list](/screenshots/list-group.png?raw=true "Group list")
![Set password](/screenshots/password.png?raw=true "Set password")

## License

This project is licensed under the GNU General Public License - see the LICENSE file for details.

## Literature

* [SAMBA LDAP Accounts](http://pig.made-it.com/samba-accounts.html)
