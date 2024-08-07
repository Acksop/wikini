<?php
/*
install.php
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Patrick PAUL
Copyright  2003  Eric FELDSTEIN
Copyright  2024  Emmanuel ROY
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
if (!defined('WIKINI_VERSION'))
{
	die ("acc&egrave;s direct interdit");
}
if (empty($_POST['config']))
{
	header('Location: ' . myLocation());
	die ('probl&egrave;me dans la proc&eacute;dure d\'installation');
}

// fetch configuration
$config = $config2 = $_POST["config"];

// merge existing (or default) configuration with new one
$config = array_merge($wakkaConfig, $config);
if (!$version = trim($wakkaConfig["wikini_version"])) $version = "0";

// test configuration
echo "<p><strong>Test de la configuration</strong></p>\n";
if ($version)
{
	test('V&eacute;rification mot de passe MySQL ...',
		isset($config2['mysqli_password']) && $wakkaConfig['mysqli_password'] === $config2['mysqli_password'],
		'Le mot de passe MySQL est incorrect !');
}
    test("Test connexion MySQL ...");
	$dblink = @mysqli_connect($config["mysqli_host"], $config["mysqli_user"], $config["mysqli_password"], null, $this->config["mysqli_port"]);
    @mysqli_query('SET NAMES utf8');
    @mysqli_set_charset('utf8');

$testdb = test("Recherche base de donn&eacute;es ...",
	@mysqli_select_db($dblink, $config["mysqli_database"]),
	"La base de donn&eacute;es que vous avez choisie n'existe pas. Nous allons tenter de la cr&eacute;er...",
	0);
if($testdb == 1)
{
	test("Tentative de cr&eacute;ation de la base de donn&eacute;es...",
		@mysqli_query( $dblink,"CREATE DATABASE ".$config["mysqli_database"]),
		"Cr&eacute;ation de la base impossible. Vous devez cr&eacute;er cette base manuellement avant d'installer WikiNi !");
	test("Recherche base de donn&eacute;es ...",
		@mysqli_select_db($dblink,$config["mysqli_database"]),
		"La base de donn&eacute;es que vous avez choisie n'existe pas, vous devez la cr&eacute;er avant d'installer WikiNi !",
		1);
}

if (!$version || empty($_POST['admin_login']))
{
	$admin_name = $_POST["admin_name"];
	$admin_email = $_POST["admin_email"];
	$admin_password = $_POST["admin_password"];
	$admin_password_conf = $_POST["admin_password_conf"];
	test('V&eacute;rification du mot de passe Administrateur...',
		strlen($admin_password) >= 6,
		'Le mot de passe est trop court',
		1);
	test('V&eacute;rification de l\'identit&eacute; des mots de passes administrateurs',
		$admin_password == $admin_password_conf,
		'Les mots de passe Administrateur sont diff&eacute;rents',
		1);
}
else
{
	$admin_name = $_POST['admin_login'];
	unset($admin_password);
}

// do installation stuff
switch ($version) {
// new installation
    case "0":
        echo "<b>Installation</b><br>\n";
        test("Creation table page...",
            @mysqli_query($dblink,
                "CREATE TABLE `" . $config["table_prefix"] . "pages` (" .
                "`id` int(10) unsigned NOT NULL AUTO_INCREMENT," .
                "`tag` varchar(50) NOT NULL DEFAULT ''," .
                "`title` varchar(512) DEFAULT ''," .
                "`description` varchar(512) DEFAULT ''," .
                "`keywords` varchar(255) DEFAULT ''," .
                "`time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'," .
                "`body` text NOT NULL," .
                "`body_r` text NOT NULL," .
                "`owner` varchar(50) NOT NULL DEFAULT ''," .
                "`user` varchar(50) NOT NULL DEFAULT ''," .
                "`latest` enum('Y','N') NOT NULL DEFAULT 'N'," .
                "`handler` varchar(30) NOT NULL DEFAULT 'page'," .
                "`comment_on` varchar(50) NOT NULL DEFAULT ''," .
                "PRIMARY KEY (`id`)," .
                "KEY `idx_tag` (`tag`)," .
                "KEY `idx_time` (`time`)," .
                "KEY `idx_latest` (`latest`)," .
                "KEY `idx_comment_on` (`comment_on`)," .
                "FULLTEXT KEY `tag` (`tag`,`body`)" .
                ") ENGINE=MyISAM DEFAULT CHARSET=utf8;"), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
        test("Creation table ACL ...",
            @mysqli_query($dblink,
                "CREATE TABLE `" . $config["table_prefix"] . "acls` (" .
                "`page_tag` varchar(255) NOT NULL DEFAULT ''," .
                "`privilege` varchar(20) NOT NULL DEFAULT ''," .
                "`list` text NOT NULL," .
                "PRIMARY KEY (`page_tag`,`privilege`)" .
                ") ENGINE=MyISAM DEFAULT CHARSET=utf8;"), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
        test("Creation table link ...",
            @mysqli_query($dblink,
                "CREATE TABLE `" . $config["table_prefix"] . "_links` (" .
                "`from_tag` char(50) NOT NULL DEFAULT ''," .
                "`to_tag` char(50) NOT NULL DEFAULT ''," .
                "UNIQUE KEY `from_tag` (`from_tag`,`to_tag`)," .
                "KEY `idx_from` (`from_tag`)," .
                "KEY `idx_to` (`to_tag`)" .
                ") ENGINE=MyISAM DEFAULT CHARSET=utf8;"), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
        test("Creation table referrer ...",
            @mysqli_query($dblink,
                "CREATE TABLE `" . $config["table_prefix"] . "referrers` (" .
                "`page_tag` char(50) NOT NULL DEFAULT ''," .
                "`referrer` char(150) NOT NULL DEFAULT ''," .
                "`time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'," .
                "KEY `idx_page_tag` (`page_tag`)," .
                "KEY `idx_time` (`time`)" .
                ") ENGINE=MyISAM DEFAULT CHARSET=utf8;"), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
        test("Creation table user ...",
            @mysqli_query($dblink,
                "CREATE TABLE `" . $config["table_prefix"] . "users` (" .
                "`name` varchar(255) NOT NULL DEFAULT ''," .
                "`password` varchar(255) DEFAULT ''," .
                "`email` varchar(255) DEFAULT ''," .
                "`motto` text DEFAULT ''," .
                "`revisioncount` int(10) unsigned DEFAULT 20," .
                "`changescount` int(10) unsigned DEFAULT 50," .
                "`doubleclickedit` enum('Y','N') DEFAULT 'Y'," .
                "`signuptime` datetime DEFAULT '0000-00-00 00:00:00'," .
                "`show_comments` enum('Y','N') DEFAULT 'N'," .
                "PRIMARY KEY (`name`)," .
                "KEY `idx_name` (`name`)," .
                "KEY `idx_signuptime` (`signuptime`)" .
                ") ENGINE=MyISAM DEFAULT CHARSET=utf8;"), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
        test("Creation table triplets ...",
            @mysqli_query($dblink,
                "CREATE TABLE `" . $config['table_prefix'] . "triples` (" .
                "`id` int(10) unsigned NOT NULL AUTO_INCREMENT," .
                "`resource` varchar(255) NOT NULL DEFAULT ''," .
                "`property` varchar(255) NOT NULL DEFAULT ''," .
                "`value` text NOT NULL," .
                "PRIMARY KEY (`id`)," .
                "KEY `resource` (`resource`)," .
                "KEY `property` (`property`)" .
                ") ENGINE=MyISAM DEFAULT CHARSET=utf8;"), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
        test("Creation compte admin ...",
            @mysqli_query($dblink,
                "insert into " . $config["table_prefix"] . "users set " .
                "signuptime = now(), " .
                "name = '" . mysqli_escape_string($admin_name) . "', " .
                "email = '" . mysqli_escape_string($admin_email) . "', " .
                "password = md5('" . mysqli_escape_string($admin_password) . "')"), 0);
        $wiki = new Wiki($config);
        $wiki->SetGroupACL("admins", $admin_name);

        //insertion des pages de documentation et des pages standards
        $d = dir("setup/doc/");
        while ($doc = $d->read()) {
            if (is_dir($doc) || substr($doc, -4) != '.txt')
                continue;
            $pagecontent = implode('', file("setup/doc/$doc"));
            if ($doc == '_root_page.txt') {
                $pagename = $config["root_page"];
            } else {
                $pagename = substr($doc, 0, strpos($doc, '.txt'));
            }

            $sql = "Select tag from " . $config["table_prefix"] . "pages where tag='$pagename'";

            // Insert documentation page if not present (a previous failed installation ?)
            if (($r = @mysqli_query($dblink, $sql)) && (mysqli_num_rows($r) == 0)) {

                $sql = "Insert into " . $config["table_prefix"] . "pages " .
                    "set tag = '$pagename', " .
                    "body = '" . mysqli_escape_string($dblink,$pagecontent) . "', " .
                    "user = '" . mysqli_escape_string($dblink,$admin_name) . "', " .
                    "owner = '" . mysqli_escape_string($dblink,$admin_name) . "', " .
                    "time = now(), " .
                    "latest = 'Y'";

                test("Insertion de la page $pagename ...", @mysqli_query($dblink, $sql), "?", 0);

                // update table_links
                $wiki->SetPage($wiki->LoadPage($pagename, "", 0));
                $wiki->ClearLinkTable();
                $wiki->StartLinkTracking();
                $wiki->TrackLinkTo($pagename);
                $dummy = $wiki->Header();
                $dummy .= $wiki->Format($pagecontent);
                $dummy .= $wiki->Footer();
                $wiki->StopLinkTracking();
                $wiki->WriteLinkTable();
                $wiki->ClearLinkTable();
            } else {
                test("Insertion de la page $pagename ...", 0, "Existe d&eacute;j&agrave;.", 0);
            }

        }
        break;

    // The funny upgrading stuff. Make sure these are in order! //
    case "0.1":
        echo "<b>En cours de mise &agrave; jour de WikiNi 0.1</b><br>\n";
        test("Modification trÃ¨s lÃ©gÃ¨re de la table des pages...",
            @mysqli_query($dblink, "alter table " . $config["table_prefix"] . "pages".
            " add body_r text not null default '' after body"), "Already done? Hmm!", 0);
    // continue through the upgrading process to create the triple's table
    case '0.4.0':
    case '0.4.1':
    case '0.4.2':
    case '0.4.3':
    case '0.4.4':
    case '0.5.0':
        test("Creation table triplets ...",
            @mysqli_query($dblink, 'CREATE TABLE `' . $config['table_prefix'] . 'triples` (' .
                '  `id` int(10) unsigned NOT NULL auto_increment,' .
                '  `resource` varchar(255) NOT NULL default \'\',' .
                '  `property` varchar(255) NOT NULL default \'\',' .
                '  `value` text NOT NULL default \'\',' .
                '  PRIMARY KEY  (`id`),' .
                '  KEY `resource` (`resource`),' .
                '  KEY `property` (`property`)' .
                ') TYPE=MyISAM'), "D&eacute;j&agrave; cr&eacute;&eacute;e ?", 0);
        if (!empty($admin_password)) {
            test("Creation compte admin ...",
                @mysqli_query($dblink,
                    "insert into " . $config["table_prefix"] . "users set " .
                    "signuptime = now(), " .
                    "name = '" . mysqli_escape_string($dblink,$admin_name) . "', " .
                    "email = '" . mysqli_escape_string($dblink,$admin_email) . "', " .
                    "password = md5('" . mysqli_escape_string($dblink,$admin_password) . "')"), "D&eacute;j&agrave; existant ?", 0);
        }
        $wiki = new Wiki($config);
        test("Insertion de l'utilisateur sp&eacute;cifi&eacute; dans le groupe admin ...",
            !$wiki->SetGroupACL("admins", $admin_name), 0);
    case '0.6.0':
        test("Modification de la table des pages afin de permettre une mise a jour du head-title, meta-keyword et meta-description ...",
            @mysqli_query($dblink, "ALTER TABLE `" . $config['table_prefix'] . "pages` " .
                "ADD `title` varchar(512) DEFAULT '' AFTER `tag`," .
                "ADD `description` varchar(512) DEFAULT '' AFTER `title`," .
                "ADD `keywords` varchar(255) DEFAULT '' AFTER `description`;"), "Already done? Hmm!", 0);
}
?>

<p>
A l'&eacute;tape suivante, le programme d'installation va essayer
d'&eacute;crire le fichier de configuration <tt><?php echo  $wakkaConfigLocation ?></tt>.
Assurez vous que le serveur web a bien le droit d'&eacute;crire dans ce fichier, sinon vous devrez le modifier manuellement.  </p>

<form action="<?php echo  myLocation(); ?>?installAction=writeconfig" method="POST">
<input type="hidden" name="config" value="<?php echo  htmlspecialchars(serialize($config)) ?>">
<input type="submit" value="Continuer">
</form>
