<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use Symfony\Contracts\Translation\TranslatorInterface;

class EcoData
{
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
            $ecoData->combinedFuelConsumptionMin = $wltp['combinedMin'] ?? null;
            $ecoData->combinedFuelConsumptionMax = $wltp['combined'] ?? null;
        }
        if ($hasBattery) {
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
        $ecoData->rangeMin = $wltp['rangeMin'] ?? null;
        $ecoData->rangeMax = $wltp['range'] ?? null;
        // NEFZ:
        if ($hasFossilFuel) {
            $ecoData->nefzCombinedFuelConsumptionMin = $nefz['combinedMin'] ?? null;
            $ecoData->nefzCombinedFuelConsumptionMax = $nefz['combined'] ?? null;
        }
        if ($hasBattery) {
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
        $ecoData->nefzRangeMin = $nefz['rangeMin'] ?? null;
        $ecoData->nefzRangeMax = $nefz['range'] ?? null;

        return $ecoData;
    }

    public function getText(TranslatorInterface $translator, ?string $separator = '; '): string
    {
        $text = [];

        $text[] = $this->getEcoText($translator, 'combinedFuelConsumption', true, 1);
        $text[] = $this->getEcoText($translator, 'combinedPowerConsumption', true, 2);

        $hasConsumption = ($this->nefzCombinedFuelConsumptionMax || $this->nefzCombinedPowerConsumptionMax || $this->combinedFuelConsumptionMax || $this->combinedPowerConsumptionMax);
        $isZeroEmissionVehicle = !($this->co2EmissionMin || $this->co2EmissionMax || $this->nefzCo2EmissionMin || $this->nefzCo2EmissionMax);

        if ($isZeroEmissionVehicle && $hasConsumption) {
            $text[] = $translator->trans('ecoData.label.co2Emission').' '.$translator->trans('ecoData.label.zeroEmission');
        } else {
            $text[] = $this->getEcoText($translator, 'co2Emission', true, 0);
        }

        $text[] = $this->getEcoText($translator, 'range', true, 0);
        $text[] = $this->getEcoText($translator, 'energyEfficiencyClass', false);

        $text = array_filter($text, static fn($v) => null !== $v);

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
            }

            if ($hasWltp) {
                if ($hasNefz) {
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