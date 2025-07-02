<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Response;

/**
 * Class ApiResponse
 * Tüm API yanıtları için standart response sınıfı
 */
class ApiResponse
{
    /** @var bool */
    private bool $success;

    /** @var string|null */
    private ?string $message;

    /** @var mixed */
    private $data;

    /**
     * ApiResponse constructor.
     * @param bool $success İşlem başarılı mı?
     * @param string|null $message Mesaj (opsiyonel)
     * @param mixed $data Veri (opsiyonel)
     */
    public function __construct(bool $success, ?string $message = null, $data = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Başarılı yanıt oluştur
     * @param mixed $data
     * @param string|null $message
     * @return self
     */
    public static function success($data = null, ?string $message = null): self
    {
        return new self(true, $message, $data);
    }

    /**
     * Hata yanıtı oluştur
     * @param string $message
     * @param mixed $data
     * @return self
     */
    public static function error(string $message, $data = null): self
    {
        return new self(false, $message, $data);
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Response'u array'e çevir
     * @return array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data
        ];
    }
} 