<?php

namespace Marello\Bundle\CoreBundle\Model;

use Oro\Bundle\LocaleBundle\Entity\Localization;

trait LocalizationTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\LocaleBundle\Entity\Localization")
     * @ORM\JoinColumn(name="localization_id", nullable=true)
     *
     * @var Localization
     */
    protected $localization;

    /**
     * @return String
     */
    public function getLocalization()
    {
        return $this->localization;
    }

    /**
     * @return $this
     */
    public function setLocalization($localization)
    {
        $this->localization = $localization;

        return $this;
    }
}