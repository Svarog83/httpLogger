<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Index(name: 'ipIndex', columns: ['ip'])]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $url;

    #[ORM\Column(type: 'text')]
    private $requestHeaders;

    #[ORM\Column(type: 'text', nullable: true)]
    private $requestBody;

    #[ORM\Column(type: 'text')]
    private $responseHeaders;

    #[ORM\Column(type: 'text', nullable: true)]
    private $responseBody;

    #[ORM\Column(type: 'integer')]
    private $responseCode;

    #[ORM\Column(type: 'bigint')]
    private $ip;

    #[ORM\Column(type: 'datetime_immutable')]
    private $processedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getRequestHeaders(): ?string
    {
        return $this->requestHeaders;
    }

    public function setRequestHeaders(string $requestHeaders): self
    {
        $this->requestHeaders = $requestHeaders;

        return $this;
    }

    public function getRequestBody(): ?string
    {
        return $this->requestBody;
    }

    public function setRequestBody(?string $requestBody): self
    {
        $this->requestBody = $requestBody;

        return $this;
    }

    public function getResponseHeaders(): ?string
    {
        return $this->responseHeaders;
    }

    public function setResponseHeaders(string $responseHeaders): self
    {
        $this->responseHeaders = $responseHeaders;

        return $this;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    public function setResponseBody(?string $responseBody): self
    {
        $this->responseBody = $responseBody;

        return $this;
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }

    public function setResponseCode(int $responseCode): self
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function setProcessedAt(\DateTimeImmutable $processedAt): self
    {
        $this->processedAt = $processedAt;

        return $this;
    }
}
