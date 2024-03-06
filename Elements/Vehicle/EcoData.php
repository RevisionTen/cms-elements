<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use Symfony\Contracts\Translation\TranslatorInterface;

class EcoData
{
    public ?string $fuelType = null;

    public ?string $co2ClassMin = null;
    public ?string $co2ClassMax = null;

    public ?string $co2ClassEmptyBatteryMin = null;
    public ?string $co2ClassEmptyBatteryMax = null;

    public ?int $co2EmissionMin = null;
    public ?int $co2EmissionMax = null;

    public ?int $co2EmissionWeightedMin = null;
    public ?int $co2EmissionWeightedMax = null;

    public ?int $rangeMin = null;
    public ?int $rangeMax = null;

    public ?float $combinedFuelConsumptionMin = null;
    public ?float $combinedFuelConsumptionMax = null;

    public ?float $combinedPowerConsumptionMin = null;
    public ?float $combinedPowerConsumptionMax = null;

    public ?int $power = null;

    public ?int $horsepower = null;

    public ?int $weight = null;

    public ?int $cubicCapacity = null;

    public ?string $fuel = null;

    public static function createFromElements(array $wltpElement, array $nefzElement): self
    {
        $wltp = $wltpElement['data'] ?? [];

        $fuelType = $wltp['fuelType'] ?? null;
        $hasFossilFuel = 'electricity' !== $fuelType;
        #$hasFossilFuel = ($wltp['combined'] ?? null) !== null;
        $hasBattery = 'electricity' === $fuelType || 'hybrid' === $fuelType || 'hybrid_petrol' === $fuelType || 'hybrid_diesel' === $fuelType;
        #$hasBattery = ($wltp['combinedPowerConsumption'] ?? null) !== null;

        $ecoData = new self();
        $ecoData->fuelType = $fuelType;

        if ($hasFossilFuel) {
            $ecoData->cubicCapacity = !empty($wltp['cubicCapacity']) ? (int) $wltp['cubicCapacity'] : null;
            $ecoData->combinedFuelConsumptionMin = $wltp['combinedMin'] ?? null;
            $ecoData->combinedFuelConsumptionMax = $wltp['combined'] ?? null;
        }
        if ($hasBattery) {
            $ecoData->rangeMin = $wltp['rangeMin'] ?? null;
            $ecoData->rangeMax = $wltp['range'] ?? null;
            $ecoData->combinedPowerConsumptionMin = $wltp['combinedPowerConsumptionMin'] ?? null;
            $ecoData->combinedPowerConsumptionMax = $wltp['combinedPowerConsumption'] ?? null;
        }
        if ($hasBattery && !$hasFossilFuel) {
            // Pure BEV.
            $ecoData->co2EmissionMin = null;
            $ecoData->co2EmissionMax = 0;
            $ecoData->co2ClassMin = null;
            $ecoData->co2ClassMax = 'A';
        } else {
            $ecoData->co2EmissionMin = $wltp['co2EmissionMin'] ?? null;
            $ecoData->co2EmissionMax = $wltp['co2Emission'] ?? null;
            $ecoData->co2EmissionWeightedMin = $wltp['co2EmissionWeightedMin'] ?? null;
            $ecoData->co2EmissionWeightedMax = $wltp['co2EmissionWeighted'] ?? null;
        }
        if (!$hasBattery && $hasFossilFuel) {
            // Pure ICE.
            $ecoData->fuel = $wltp['fuel'] ?? null;
            if (empty($ecoData->fuel ) && ('diesel' === $fuelType || 'petrol' === $fuelType)) {
                // Infer fuel from fuelType.
                $ecoData->fuel  = $fuelType;
            }
        }
        $ecoData->power = !empty($wltp['power']) ? (int) $wltp['power'] : null;
        $ecoData->horsepower = !empty($wltp['horsepower']) ? (int) $wltp['horsepower'] : null;
        $ecoData->weight = !empty($wltp['weight']) ? (int) $wltp['weight'] : null;

        return $ecoData;
    }

    public function hasFuelConsumption(): bool
    {
        return $this->combinedFuelConsumptionMax !== null;
    }

    public function hasPowerConsumption(): bool
    {
        return $this->combinedPowerConsumptionMax !== null;
    }

    public function hasConsumption(): bool
    {
        return $this->hasFuelConsumption() || $this->hasPowerConsumption();
    }

    public function isZeroEmissionVehicle(): bool
    {
        return !($this->co2EmissionMin || $this->co2EmissionMax);
    }

    public function hasWltp(): bool
    {
        if ('hydrogen' !== $this->fuelType && $this->hasFuelConsumption() && $this->isZeroEmissionVehicle()) {
            // This vehicle has fuel consumption but no CO2 emissions. It can't be valid.
            return false;
        }

        return $this->combinedFuelConsumptionMax || $this->combinedPowerConsumptionMax;
    }

    public function getCo2Class(?int $emission): ?string
    {
        if (null === $emission) {
            return null;
        }

        if ($emission === 0) {
            return 'A';
        }
        if ($emission <= 95) {
            return 'B';
        }
        if ($emission <= 115) {
            return 'C';
        }
        if ($emission <= 135) {
            return 'D';
        }
        if ($emission <= 155) {
            return 'E';
        }
        if ($emission <= 175) {
            return 'F';
        }
        return 'G';
    }

    public function getText(TranslatorInterface $translator, ?string $separator = '; ', ?array $excludedFields = null): string
    {
        if (!$this->hasWltp()) {
            return '';
        }

        $this->co2ClassMin = $this->getCo2Class($this->co2EmissionMin);
        $this->co2ClassMax = $this->getCo2Class($this->co2EmissionMax);
        $this->co2ClassEmptyBatteryMin = $this->getCo2Class($this->co2EmissionWeightedMin);
        $this->co2ClassEmptyBatteryMax = $this->getCo2Class($this->co2EmissionWeightedMax);

        $text = [];

        $fuelUnit = 'l/100km';
        if ('cng' === $this->fuelType || 'hydrogen' === $this->fuelType) {
            $fuelUnit = 'kg/100km';
        }

        $text['combinedFuelConsumption'] = $this->getEcoText($translator, 'combinedFuelConsumption', true, 1, $fuelUnit);
        $text['combinedPowerConsumption'] = $this->getEcoText($translator, 'combinedPowerConsumption', true, 2);

        if ($this->isZeroEmissionVehicle() && $this->hasConsumption()) {
            $text['co2Emission'] = $translator->trans('ecoData.label.co2Emission').' '.$translator->trans('ecoData.wltp.co2Emission', ['%max%' => 0]);
        } else {
            $text['co2Emission'] = $this->getEcoText($translator, 'co2Emission', true, 0);
            $text['co2EmissionWeighted'] = $this->getEcoText($translator, 'co2EmissionWeighted', true, 0);
        }

        $text['range'] = $this->getEcoText($translator, 'range', true, 0);

        #if (null !== $this->power && null !== $this->horsepower) {
        #    if ($this->isZeroEmissionVehicle()) {
        #        // BEV
        #        $text['power'] = $translator->trans('ecoData.wltp.electricPower', [
        #            '%kw%' => $this->power,
        #            '%ps%' => $this->horsepower,
        #        ]);
        #    } elseif ($this->hasFuelConsumption() && $this->hasPowerConsumption()) {
        #        // PHEV
        #        $text['power'] = $translator->trans('ecoData.wltp.hybridPower', [
        #            '%kw%' => $this->power,
        #            '%ps%' => $this->horsepower,
        #        ]);
        #    } else {
        #        // ICE
        #        $text['power'] = $translator->trans('ecoData.wltp.power', [
        #            '%kw%' => $this->power,
        #            '%ps%' => $this->horsepower,
        #        ]);
        #    }
        #}
        #if (null !== $this->weight && $this->hasPowerConsumption()) {
        #    $text['weight'] = $translator->trans('ecoData.wltp.weight', [
        #        '%value%' => $this->weight,
        #    ]);
        #}
        #if (null !== $this->cubicCapacity && $this->hasFuelConsumption()) {
        #    $text['cubicCapacity'] = $translator->trans('ecoData.wltp.cubicCapacity', [
        #        '%value%' => $this->cubicCapacity,
        #    ]);
        #}

        if (null !== $this->fuel) {
            $fuelTypes = ['petrol', 'diesel', 'lpg', 'cng', 'gas', 'electricity', 'hybrid', 'hybrid_petrol', 'hybrid_diesel', 'hydrogen', 'other'];
            $fuel = in_array($this->fuel, $fuelTypes, true) ? $translator->trans('ecoData.fuelTypes.'.$this->fuel) : $this->fuel;
            $text['fuel'] = $translator->trans('ecoData.wltp.fuel', [
                '%value%' => $fuel,
            ]);
        } elseif ($this->fuelType) {
            $text['fuel'] = $translator->trans('ecoData.wltp.fuel', [
                '%value%' => $translator->trans('ecoData.fuelTypes.'.$this->fuelType),
            ]);
        }

        $text['co2Class'] = $this->getEcoText($translator, 'co2Class', false);
        $text['co2ClassEmptyBattery'] = $this->getEcoText($translator, 'co2ClassEmptyBattery', false);

        $text = array_filter($text, static fn($v) => null !== $v);
        if (!empty($excludedFields)) {
            $text = array_filter($text, static fn($k) => !in_array($k, $excludedFields, true), ARRAY_FILTER_USE_KEY);
        }

        return implode($separator, $text);
    }

    private function getEcoText(TranslatorInterface $translator, string $fieldName, bool $formatNumber, ?int $decimals = 2, ?string $unit = null): ?string
    {
        $wltp_min = $this->{$fieldName.'Min'} ?? null;
        $wltp_max = $this->{$fieldName.'Max'} ?? null;

        $hasWltp = null !== $wltp_min || null !== $wltp_max;
        if ($hasWltp) {
            $text = $translator->trans('ecoData.label.'.$fieldName, [
                '%unit%' => $unit,
            ]);

            if (null !== $wltp_min && null !== $wltp_max) {
                $text .= ' '.$translator->trans('ecoData.wltp.'.$fieldName.'MinMax', [
                    '%min%' => $formatNumber ? number_format($wltp_min, $decimals, ',', '.') : $wltp_min,
                    '%max%' => $formatNumber ? number_format($wltp_max, $decimals, ',', '.') : $wltp_max,
                ]);
            } elseif (null !== $wltp_max) {
                $text .= ' '.$translator->trans('ecoData.wltp.'.$fieldName, [
                    '%max%' => $formatNumber ? number_format($wltp_max, $decimals, ',', '.') : $wltp_max,
                ]);
            }

            return $text;
        }

        return null;
    }
}
