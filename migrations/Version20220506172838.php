<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220506172838 extends AbstractMigration
{
	public function getDescription(): string
	{
		return '';
	}

	public function up(Schema $schema): void
	{
		// this up() migration is auto-generated, please modify it to your needs
		$this->addSql('INSERT INTO "public"."log" ("id", "url", "request_headers", "request_body", "response_headers", "response_body", "response_code", "ip", "processed_at") VALUES (1, 
\'http://localhost/\', \'{"host":["localhost"],"user-agent":["Symfony BrowserKit"],"accept-language":["en-us,en;q=0.5"],"accept-charset":["ISO-8859-1,utf-8;q=0.7,*;q=0.7"],"request-log":["1"],"x-php-ob-level":["1"]}\', 
\'\', \'{"cache-control":["no-cache, private"],"date":["Fri, 06 May 2022 14:10:06 GMT"],"content-type":["text\/html; charset=UTF-8"],"x-robots-tag":["noindex"]}\',
		\'<!DOCTYPE html><html><body><a href="/admin/http-log">Просмотр логов</a></body></html>\', 200, 2130706433, \'2022-05-06 14:10:06\');');

		$this->addSql('INSERT INTO "public"."log" ("id", "url", "request_headers", "request_body", "response_headers", "response_body", "response_code", "ip", "processed_at") VALUES (2, 
\'http://localhost/?request-log=1\', \'{"host":["localhost"],"user-agent":["Symfony BrowserKit"],"accept-language":["en-us,en;q=0.5"],"accept-charset":["ISO-8859-1,utf-8;q=0.7,*;q=0.7"],"x-php-ob-level":["1"]}\',
\'\', \'{"content-type":["text\/html; charset=UTF-8"],"cache-control":["no-cache, private"],"date":["Thu, 05 May 2022 16:38:07 GMT"],"x-robots-tag":["noindex"]}\',
		\'<!DOCTYPE html><html><body><a href="/admin/http-log">Просмотр логов</a></body></html>\', 200, 2110306433, \'2022-05-06 14:11:15\');');
	}

	public function down(Schema $schema): void
	{

	}
}
