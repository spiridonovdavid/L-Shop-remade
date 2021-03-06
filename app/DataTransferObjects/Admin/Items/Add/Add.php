<?php
declare(strict_types=1);

namespace app\DataTransferObjects\Admin\Items\Add;

use Illuminate\Http\UploadedFile;

class Add
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var null|string
     */
    private $description;

    /**
     * @var string
     */
    private $itemType;

    /**
     * @var string
     */
    private $imageType;

    /**
     * @var null|UploadedFile
     */
    private $file;

    /**
     * @var null|string
     */
    private $imageName;

    /**
     * @var null|string
     */
    private $signature;

    /**
     * @var EnchantmentFromFrontend[]
     */
    private $enchantments;

    /**
     * @var null|string
     */
    private $extra;

    /**
     * @param string $name
     *
     * @return Add
     */
    public function setName(string $name): Add
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param null|string $description
     *
     * @return Add
     */
    public function setDescription(?string $description): Add
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $itemType
     *
     * @return Add
     */
    public function setItemType(string $itemType): Add
    {
        $this->itemType = $itemType;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemType(): string
    {
        return $this->itemType;
    }

    /**
     * @param string $imageType
     *
     * @return Add
     */
    public function setImageType(string $imageType): Add
    {
        $this->imageType = $imageType;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageType(): string
    {
        return $this->imageType;
    }

    /**
     * @param UploadedFile|null $file
     *
     * @return Add
     */
    public function setFile(?UploadedFile $file): Add
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param null|string $imageName
     *
     * @return Add
     */
    public function setImageName(?string $imageName): Add
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @param string $signature
     *
     * @return Add
     */
    public function setSignature(?string $signature): Add
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSignature(): ?string
    {
        return $this->signature;
    }

    /**
     * @param EnchantmentFromFrontend[] $enchantments
     *
     * @return Add
     */
    public function setEnchantments(array $enchantments): Add
    {
        $this->enchantments = $enchantments;

        return $this;
    }

    /**
     * @return EnchantmentFromFrontend[]
     */
    public function getEnchantments(): array
    {
        return $this->enchantments;
    }

    /**
     * @param null|string $extra
     *
     * @return Add
     */
    public function setExtra(?string $extra): Add
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExtra(): ?string
    {
        return $this->extra;
    }
}
