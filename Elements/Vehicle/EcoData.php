<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use RevisionTen\CarPortCommon\Model\CarDetails;
use Symfony\Contracts\Translation\TranslatorInterface;

class EcoData
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public ?string $title = null;
    public ?bool $unofficial = false;

    public ?int $power = null;
    public ?int $horsepower = null;
    public ?int $weight = null;
    public ?int $cubicCapacity = null;
    public ?string $fuel = null;
    public ?string $fuelType = null;

    public ?int $rangeMin = null;
    public ?int $rangeMax = null;

    public ?string $co2ClassMin = null;
    public ?string $co2ClassMax = null;

    public ?string $co2ClassEmptyBatteryMin = null;
    public ?string $co2ClassEmptyBatteryMax = null;

    public ?float $co2EmissionMin = null;
    public ?float $co2EmissionMax = null;
    public ?float $co2EmissionWeightedMin = null;
    public ?float $co2EmissionWeightedMax = null;

    public ?float $combinedFuelConsumptionMin = null;
    public ?float $combinedFuelConsumptionMax = null;
    public ?float $combinedFuelConsumptionWeightedMin = null;
    public ?float $combinedFuelConsumptionWeightedMax = null;

    public ?float $combinedPowerConsumptionMin = null;
    public ?float $combinedPowerConsumptionMax = null;
    public ?float $combinedPowerConsumptionWeightedMin = null;
    public ?float $combinedPowerConsumptionWeightedMax = null;

    public function createFromElements(array $wltpElement, array $nefzElement): self
    {
        $wltp = $wltpElement['data'] ?? [];

        $translator = $this->translator;
        $ecoData = new EcoData($translator);

        $ecoData->title = $wltp['title'] ?? null;
        $ecoData->unofficial = !empty($wltp['unofficial']);
        $ecoData->power = !empty($wltp['power']) ? (int) $wltp['power'] : null;
        $ecoData->horsepower = !empty($wltp['horsepower']) ? (int) $wltp['horsepower'] : null;
        $ecoData->weight = !empty($wltp['weight']) ? (int) $wltp['weight'] : null;
        $ecoData->cubicCapacity = !empty($wltp['cubicCapacity']) ? (int) $wltp['cubicCapacity'] : null;
        $ecoData->fuel = $wltp['fuel'] ?? null;
        $ecoData->fuelType = $wltp['fuelType'] ?? null;

        $ecoData->rangeMin = !empty($wltp['rangeMin']) ? (int) $wltp['rangeMin'] : null;
        $ecoData->rangeMax = !empty($wltp['range']) ? (int) $wltp['range'] : null;

        $ecoData->co2EmissionMin = $wltp['co2EmissionMin'] ?? null;
        $ecoData->co2EmissionMax = $wltp['co2Emission'] ?? null;
        $ecoData->co2EmissionWeightedMin = $wltp['co2EmissionWeightedMin'] ?? null;
        $ecoData->co2EmissionWeightedMax = $wltp['co2EmissionWeighted'] ?? null;

        $ecoData->combinedFuelConsumptionMin = $wltp['combinedMin'] ?? null;
        $ecoData->combinedFuelConsumptionMax = $wltp['combined'] ?? null;

        $ecoData->combinedFuelConsumptionWeightedMin = $wltp['combinedWeightedMin'] ?? null;
        $ecoData->combinedFuelConsumptionWeightedMax = $wltp['combinedWeighted'] ?? null;

        $ecoData->combinedPowerConsumptionMin = $wltp['combinedPowerConsumptionMin'] ?? null;
        $ecoData->combinedPowerConsumptionMax = $wltp['combinedPowerConsumption'] ?? null;

        $ecoData->combinedPowerConsumptionWeightedMin = $wltp['combinedPowerConsumptionWeightedMin'] ?? null;
        $ecoData->combinedPowerConsumptionWeightedMax = $wltp['combinedPowerConsumptionWeighted'] ?? null;

        $ecoData->calculateAllCo2Classes();

        return $ecoData;
    }

    public function createFromCar(CarDetails $car): self
    {
        $translator = $this->translator;
        $ecoData = new EcoData($translator);

        $ecoData->title = $car->title;
        $ecoData->power = $car->power;
        $ecoData->horsepower = $car->horsePower;
        $ecoData->weight = $car->emptyWeight;
        $ecoData->cubicCapacity = $car->cubicCapacity;
        $ecoData->fuel = $car->fuel;
        $ecoData->fuelType = $car->petrolType;

        $ecoData->rangeMin = $car->electricRangeMin;
        $ecoData->rangeMax = $car->electricRangeMax;
        if (!empty($ecoData->rangeMin) && empty($ecoData->rangeMax)) {
            $ecoData->rangeMax = $ecoData->rangeMin;
            $ecoData->rangeMin = null;
        }

        $ecoData->co2EmissionMax = $car->co2EmissionWLTP;
        $ecoData->co2EmissionWeightedMax = $car->co2EmissionWLTPWeighted;

        $ecoData->combinedFuelConsumptionMax = $car->combinedWLTP;
        $ecoData->combinedFuelConsumptionWeightedMax = $car->combinedWLTPWeighted;

        $ecoData->combinedPowerConsumptionMax = $car->combinedPowerConsumptionWLTP;
        $ecoData->combinedPowerConsumptionWeightedMax = $car->combinedPowerConsumptionWLTPWeighted;

        $ecoData->calculateAllCo2Classes();

        return $ecoData;
    }

    public function hasFuelConsumption(): bool
    {
        return $this->combinedFuelConsumptionMax !== null || $this->combinedFuelConsumptionWeightedMax !== null;
    }

    public function hasPowerConsumption(): bool
    {
        return $this->combinedPowerConsumptionMax !== null || $this->combinedPowerConsumptionWeightedMax !== null;
    }

    public function hasConsumption(): bool
    {
        return $this->hasFuelConsumption() || $this->hasPowerConsumption();
    }

    public function isZeroEmissionVehicle(): bool
    {
        return !($this->co2EmissionMin || $this->co2EmissionMax || $this->co2EmissionWeightedMin || $this->co2EmissionWeightedMax);
    }

    public function isHybrid(): bool
    {
        return $this->co2EmissionWeightedMax || $this->combinedFuelConsumptionWeightedMax || $this->combinedPowerConsumptionWeightedMax;
    }

    public function hasWltp(): bool
    {
        if ('hydrogen' !== $this->fuelType && $this->hasFuelConsumption() && $this->isZeroEmissionVehicle()) {
            // This vehicle has fuel consumption but no CO2 emissions. It can't be valid.
            return false;
        }

        return $this->hasConsumption();
    }

    public function calculateAllCo2Classes(): self
    {
        if ($this->isZeroEmissionVehicle()) {
            $this->co2EmissionMax = 0;
        }

        if ($this->isHybrid()) {
            $this->co2ClassMin = $this->calculateCo2Class($this->co2EmissionWeightedMin);
            $this->co2ClassMax = $this->calculateCo2Class($this->co2EmissionWeightedMax);
            $this->co2ClassEmptyBatteryMin = $this->calculateCo2Class($this->co2EmissionMin);
            $this->co2ClassEmptyBatteryMax = $this->calculateCo2Class($this->co2EmissionMax);
        } else {
            $this->co2ClassMin = $this->calculateCo2Class($this->co2EmissionMin);
            $this->co2ClassMax = $this->calculateCo2Class($this->co2EmissionMax);
        }

        return $this;
    }

    public function calculateCo2Class(?float $emission): ?string
    {
        if (null === $emission) {
            return null;
        }

        if ($emission <= 0) {
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

    public function getTextArray(): ?array
    {
        if (!$this->hasWltp()) {
            return null;
        }

        $text = [];

        $fuelUnit = 'l/100km';
        if ('cng' === $this->fuelType || 'hydrogen' === $this->fuelType) {
            $fuelUnit = 'kg/100km';
        }

        if ($this->isHybrid()) {
            $text['combinedFuelConsumption'] = $this->getEcoText($this->translator, 'combinedFuelConsumption', true, 1, $fuelUnit);
            $text['combinedFuelConsumptionWeighted'] = $this->getEcoText($this->translator, 'combinedFuelConsumptionWeighted', true, 1, $fuelUnit);
            $text['combinedPowerConsumption'] = $this->getEcoText($this->translator, 'combinedPowerConsumption', true, 2);
            $text['combinedPowerConsumptionWeighted'] = $this->getEcoText($this->translator, 'combinedPowerConsumptionWeighted', true, 2);
            $text['co2Emission'] = $this->getEcoText($this->translator, 'co2Emission', true, 0);
            $text['co2EmissionWeighted'] = $this->getEcoText($this->translator, 'co2EmissionWeighted', true, 0);
            $text['co2Class'] = $this->getEcoText($this->translator, 'co2Class', false);
            $text['co2ClassEmptyBattery'] = $this->getEcoText($this->translator, 'co2ClassEmptyBattery', false);
        } else {
            $text['combinedFuelConsumption'] = $this->getEcoText($this->translator, 'combinedFuelConsumption', true, 1, $fuelUnit);
            $text['combinedPowerConsumption'] = $this->getEcoText($this->translator, 'combinedPowerConsumption', true, 2);
            $text['co2Emission'] = $this->getEcoText($this->translator, 'co2Emission', true, 0);
            $text['co2Class'] = $this->getEcoText($this->translator, 'co2Class', false);
        }

        $text['range'] = $this->getEcoText($this->translator, 'range', true, 0);
        if ($this->isHybrid() && !empty($text['range'])) {
            // BMW recommends to label the range with "EAER" for hybrids
            $text['range'] = str_replace('Elektrische Reichweite', 'Elektrische Reichweite (EAER)', $text['range']);
        }

        if ($this->cubicCapacity !== null) {
            $text['cubicCapacity'] = $this->translator->trans('ecoData.wltp.cubicCapacity', [
                '%value%' => $this->cubicCapacity,
            ]);
        }

        if ($this->power !== null && $this->horsepower !== null) {
            if ($this->isZeroEmissionVehicle()) {
                $text['power'] = $this->translator->trans('ecoData.wltp.electricPower', [
                    '%kw%' => $this->power,
                    '%ps%' => $this->horsepower,
                ]);
            } elseif ($this->isHybrid()) {
                $text['power'] = $this->translator->trans('ecoData.wltp.hybridPower', [
                    '%kw%' => $this->power,
                    '%ps%' => $this->horsepower,
                ]);
            } else {
                $text['power'] = $this->translator->trans('ecoData.wltp.power', [
                    '%kw%' => $this->power,
                    '%ps%' => $this->horsepower,
                ]);
            }
        }

        if (null !== $this->fuel) {
            $fuelTypes = ['petrol', 'diesel', 'lpg', 'cng', 'gas', 'electricity', 'hybrid', 'hybrid_petrol', 'hybrid_diesel', 'hydrogen', 'other'];
            $fuel = in_array($this->fuel, $fuelTypes, true) ? $this->translator->trans('ecoData.fuelTypes.'.$this->fuel) : $this->fuel;
            $text['fuel'] = $this->translator->trans('ecoData.wltp.fuel', [
                '%value%' => $fuel,
            ]);
        } elseif ($this->fuelType) {
            $text['fuel'] = $this->translator->trans('ecoData.wltp.fuel', [
                '%value%' => $this->translator->trans('ecoData.fuelTypes.'.$this->fuelType),
            ]);
        }

        $text = array_filter($text, static fn($v) => null !== $v);

        return $text;
    }

    public function getText(?string $separator = '; ', ?array $excludedFields = null, ?bool $addWltpPrefix = false): string
    {
        $text = $this->getTextArray();
        if ($text === null) {
            return '';
        }
        if (!empty($excludedFields)) {
            $text = array_filter($text, static fn($k) => !in_array($k, $excludedFields, true), ARRAY_FILTER_USE_KEY);
        }

        $text = implode($separator, $text);

        if ($addWltpPrefix) {
            $text = str_replace([
                'CO₂-Emission',
                'Kraftstoffverbrauch',
                'Stromverbrauch',
                'Elektrische Reichweite',
            ], [
                'WLTP CO₂-Emission',
                'WLTP Kraftstoffverbrauch',
                'WLTP Stromverbrauch',
                'WLTP Elektrische Reichweite',
            ], $text);
        }

        return $text;
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
