<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use Symfony\Contracts\Translation\TranslatorInterface;

class EcoData
{
    public bool $showNefzPlaceholders = false;

    // WLTP

    public ?string $energyEfficiencyClassMin = null;

    public ?string $energyEfficiencyClassMax = null;

    public ?int $co2EmissionMin = null;

    public ?int $co2EmissionMax = null;

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

    // NEFZ

    public ?string $nefzEnergyEfficiencyClassMin = null;

    public ?string $nefzEnergyEfficiencyClassMax = null;

    public ?int $nefzCo2EmissionMin = null;

    public ?int $nefzCo2EmissionMax = null;

    public ?float $nefzCombinedFuelConsumptionMin = null;

    public ?float $nefzCombinedFuelConsumptionMax = null;

    public ?float $nefzCombinedPowerConsumptionMin = null;

    public ?float $nefzCombinedPowerConsumptionMax = null;

    public static function createFromElements(array $wltpElement, array $nefzElement): self
    {
        $wltp = $wltpElement['data'] ?? [];
        $nefz = $nefzElement['data'] ?? [];

        $fuelType = $wltp['fuelType'] ?? ($nefz['fuelType'] ?? null);
        $hasFossilFuel = 'electricity' !== $fuelType && 'hydrogen' !== $fuelType;
        $hasBattery = 'electricity' === $fuelType || 'hydrogen' === $fuelType || 'hybrid' === $fuelType || 'hybrid_petrol' === $fuelType || 'hybrid_diesel' === $fuelType;

        $ecoData = new self();
        // WLTP:
        if ($hasFossilFuel) {
            $ecoData->cubicCapacity = !empty($wltp['cubicCapacity']) ? (int) $wltp['cubicCapacity'] : null;
            $ecoData->fuel = $wltp['fuel'] ?? null;
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
            $ecoData->co2EmissionMin = $wltp['co2EmissionMin'] ?? null;
            $ecoData->co2EmissionMax = $wltp['co2Emission'] ?? 0;
        } else {
            $ecoData->co2EmissionMin = $wltp['co2EmissionMin'] ?? null;
            $ecoData->co2EmissionMax = $wltp['co2Emission'] ?? null;
        }
        $ecoData->energyEfficiencyClassMin = $wltp['energyEfficiencyClassMin'] ?? null;
        $ecoData->energyEfficiencyClassMax = $wltp['energyEfficiencyClass'] ?? null;
        $ecoData->power = !empty($wltp['power']) ? (int) $wltp['power'] : null;
        $ecoData->horsepower = !empty($wltp['horsepower']) ? (int) $wltp['horsepower'] : null;
        $ecoData->weight = !empty($wltp['weight']) ? (int) $wltp['weight'] : null;

        // NEFZ:
        if ($hasFossilFuel) {
            $ecoData->nefzCombinedFuelConsumptionMin = $nefz['combinedMin'] ?? null;
            $ecoData->nefzCombinedFuelConsumptionMax = $nefz['combined'] ?? null;
        }
        if ($hasBattery) {
            $ecoData->nefzRangeMin = $nefz['rangeMin'] ?? null;
            $ecoData->nefzRangeMax = $nefz['range'] ?? null;
            $ecoData->nefzCombinedPowerConsumptionMin = $nefz['combinedPowerConsumptionMin'] ?? null;
            $ecoData->nefzCombinedPowerConsumptionMax = $nefz['combinedPowerConsumption'] ?? null;
        }
        if ($hasBattery && !$hasFossilFuel) {
            $ecoData->nefzCo2EmissionMin = $nefz['co2EmissionMin'] ?? null;
            $ecoData->nefzCo2EmissionMax = $nefz['co2Emission'] ?? 0;
        } else {
            $ecoData->nefzCo2EmissionMin = $nefz['co2EmissionMin'] ?? null;
            $ecoData->nefzCo2EmissionMax = $nefz['co2Emission'] ?? null;
        }
        $ecoData->nefzEnergyEfficiencyClassMin = $nefz['energyEfficiencyClassMin'] ?? null;
        $ecoData->nefzEnergyEfficiencyClassMax = $nefz['energyEfficiencyClass'] ?? null;

        return $ecoData;
    }

    public function hasFuelConsumption(): bool
    {
        return $this->nefzCombinedFuelConsumptionMax || $this->combinedFuelConsumptionMax;
    }

    public function hasPowerConsumption(): bool
    {
        return $this->nefzCombinedPowerConsumptionMax || $this->combinedPowerConsumptionMax;
    }

    public function hasConsumption(): bool
    {
        return $this->hasFuelConsumption() || $this->hasPowerConsumption();
    }

    public function isZeroEmissionVehicle(): bool
    {
        return !($this->co2EmissionMin || $this->co2EmissionMax || $this->nefzCo2EmissionMin || $this->nefzCo2EmissionMax);
    }

    public function hasNefz(): bool
    {
        return $this->nefzCombinedFuelConsumptionMax || $this->nefzCombinedPowerConsumptionMax;
    }

    public function hasWltp(): bool
    {
        return $this->combinedFuelConsumptionMax || $this->combinedPowerConsumptionMax;
    }

    public function getText(TranslatorInterface $translator, ?string $separator = '; ', ?array $excludedFields = null): string
    {
        $text = [];

        $text['combinedFuelConsumption'] = $this->getEcoText($translator, 'combinedFuelConsumption', true, 1);
        $text['combinedPowerConsumption'] = $this->getEcoText($translator, 'combinedPowerConsumption', true, 2);

        if ($this->isZeroEmissionVehicle() && $this->hasConsumption()) {
            $co2Emission = '';
            $hasNefz = $this->hasNefz();
            $hasWltp = $this->hasWltp();
            if (($this->showNefzPlaceholders && $hasWltp) || $hasNefz) {
                $emissions = $hasNefz ? 0 : '-';
                $co2Emission = $translator->trans('ecoData.label.co2Emission').' '.$translator->trans('ecoData.nefz.co2Emission', ['%max%' => $emissions]);
            }
            if ($hasWltp) {
                $co2Emission .= $translator->trans('ecoData.label.co2Emission').' '.$translator->trans('ecoData.wltp.co2Emission', ['%max%' => 0]);
            }
            $text['co2Emission'] = $co2Emission;
        } else {
            $text['co2Emission'] = $this->getEcoText($translator, 'co2Emission', true, 0);
        }

        $text['range'] = $this->getEcoText($translator, 'range', true, 0);

        if (null !== $this->power && null !== $this->horsepower) {
            if ($this->isZeroEmissionVehicle()) {
                // BEV
                $text['power'] = $translator->trans('ecoData.wltp.electricPower', [
                    '%kw%' => $this->power,
                    '%ps%' => $this->horsepower,
                ]);
            } elseif ($this->hasFuelConsumption() && $this->hasPowerConsumption()) {
                // PHEV
                $text['power'] = $translator->trans('ecoData.wltp.hybridPower', [
                    '%kw%' => $this->power,
                    '%ps%' => $this->horsepower,
                ]);
            } else {
                // ICE
                $text['power'] = $translator->trans('ecoData.wltp.power', [
                    '%kw%' => $this->power,
                    '%ps%' => $this->horsepower,
                ]);
            }
        }
        if (null !== $this->weight && $this->hasPowerConsumption()) {
            $text['weight'] = $translator->trans('ecoData.wltp.weight', [
                '%value%' => $this->weight,
            ]);
        }
        if (null !== $this->cubicCapacity && $this->hasFuelConsumption()) {
            $text['cubicCapacity'] = $translator->trans('ecoData.wltp.cubicCapacity', [
                '%value%' => $this->cubicCapacity,
            ]);
        }
        if ($this->hasFuelConsumption() && !$this->hasPowerConsumption()) {
            $fuelFallback = null;
            $fuelType = $wltp['fuelType'] ?? ($nefz['fuelType'] ?? null);
            switch ($fuelType) {
                case 'hybrid_petrol':
                case 'petrol':
                    $fuelFallback = 'petrol';
                    break;
                case 'hybrid_diesel':
                case 'diesel':
                    $fuelFallback = 'diesel';
                    break;
            }
            $fuelFallback = $fuelFallback ? $translator->trans('ecoData.fuelTypes.'.$fuelFallback) : null;
            $fuel = $this->fuel ?? $fuelFallback;
            if ($fuel) {
                $text['fuel'] = $translator->trans('ecoData.wltp.fuel', [
                    '%value%' => $fuel,
                ]);
            }
        }

        $text['energyEfficiencyClass'] = $this->getEcoText($translator, 'energyEfficiencyClass', false);

        $text = array_filter($text, static fn($v) => null !== $v);
        if (!empty($excludedFields)) {
            $text = array_filter($text, static fn($k) => !in_array($k, $excludedFields, true), ARRAY_FILTER_USE_KEY);
        }

        return implode($separator, $text);
    }

    private function getEcoText(TranslatorInterface $translator, string $fieldName, bool $formatNumber, ?int $decimals = 2): ?string
    {
        $wltp_min = $this->{$fieldName.'Min'} ?? null;
        $wltp_max = $this->{$fieldName.'Max'} ?? null;

        $nefz_min = $this->{'nefz'.ucfirst($fieldName).'Min'} ?? null;
        $nefz_max = $this->{'nefz'.ucfirst($fieldName).'Max'} ?? null;

        if (null !== $wltp_min || null !== $wltp_max || null !== $nefz_min || null !== $nefz_max) {
            $text = $translator->trans('ecoData.label.'.$fieldName);

            $hasWltp = null !== $wltp_min || null !== $wltp_max;
            $hasNefz = null !== $nefz_min || null !== $nefz_max;

            // For NEFZ placeholders to be shown, the property must exist, be null and have a WLTP counterpart.
            $nefzProperty = 'nefz'.ucfirst($fieldName).'Max';
            $hasNefzPlaceholder = $this->showNefzPlaceholders && !$hasNefz && $hasWltp && property_exists(__CLASS__, $nefzProperty);

            if ($hasNefz) {
                if (null !== $nefz_min && null !== $nefz_max) {
                    $text .= ' '.$translator->trans('ecoData.nefz.'.$fieldName.'MinMax', [
                        '%min%' => $formatNumber ? number_format($nefz_min, $decimals, ',', '.') : $nefz_min,
                        '%max%' => $formatNumber ? number_format($nefz_max, $decimals, ',', '.') : $nefz_max,
                    ]);
                } elseif (null !== $nefz_max) {
                    $text .= ' '.$translator->trans('ecoData.nefz.'.$fieldName, [
                        '%max%' => $formatNumber ? number_format($nefz_max, $decimals, ',', '.') : $nefz_max,
                    ]);
                }
            } elseif ($hasNefzPlaceholder) {
                $text .= ' '.$translator->trans('ecoData.nefz.'.$fieldName, [
                    '%max%' => '-',
                ]);
            }

            if ($hasWltp) {
                if ($hasNefz || $hasNefzPlaceholder) {
                    $text .= ';';
                }
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
            }

            return $text;
        }

        return null;
    }
}