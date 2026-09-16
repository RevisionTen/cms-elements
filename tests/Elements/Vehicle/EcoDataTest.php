<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Tests\Elements\Vehicle;

use PHPUnit\Framework\TestCase;
use RevisionTen\CmsElements\Elements\Vehicle\EcoData;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

class EcoDataTest extends TestCase
{
    /**
     * @dataProvider hybridFuelTypes
     */
    public function testHybridElementRendersWeightedPowerConsumptionAndRange(string $fuelType, string $fuel): void
    {
        $ecoData = $this->createEcoData()->createFromElements(['data' => [
            'title' => 'JAECOO 7 PHEV',
            'fuelType' => $fuelType,
            'fuel' => $fuel,
            'combined' => 6.0,
            'combinedWeighted' => 2.4,
            'combinedPowerConsumption' => null,
            'combinedPowerConsumptionWeighted' => 15.1,
            'range' => 90,
            'co2EmissionWeighted' => 54.0,
            'co2Class' => 'B',
            'co2ClassEmptyBattery' => 'E',
        ]], []);

        self::assertSame(15.1, $ecoData->combinedPowerConsumptionWeightedMax);
        self::assertNull($ecoData->combinedPowerConsumptionMax);
        self::assertSame(90, $ecoData->rangeMax);
        self::assertStringContainsString('Stromverbrauch gewichtet kombiniert in kWh/100 km: 15,10', $ecoData->getText());
        self::assertStringContainsString('Elektrische Reichweite (EAER) in km: 90', $ecoData->getText());
        self::assertStringContainsString('Kraftstoffverbrauch gewichtet kombiniert in l/100km: 2,4', $ecoData->getText());
    }

    /**
     * @dataProvider hybridFuelTypes
     */
    public function testHybridPreservesElectricValuesWithoutWeightedConsumption(string $fuelType, string $fuel): void
    {
        $ecoData = $this->createEcoData();
        $ecoData->fuelType = $fuelType;
        $ecoData->fuel = $fuel;
        $ecoData->combinedPowerConsumptionMin = 14.0;
        $ecoData->combinedPowerConsumptionMax = 15.1;
        $ecoData->rangeMin = 80;
        $ecoData->rangeMax = 90;

        $ecoData->removeInvalidValues();

        self::assertSame(14.0, $ecoData->combinedPowerConsumptionMin);
        self::assertSame(15.1, $ecoData->combinedPowerConsumptionMax);
        self::assertSame(80, $ecoData->rangeMin);
        self::assertSame(90, $ecoData->rangeMax);
    }

    public function hybridFuelTypes(): array
    {
        return [
            'petrol hybrid' => ['hybrid_petrol', 'Benzin'],
            'diesel hybrid' => ['hybrid_diesel', 'Diesel'],
            'generic petrol hybrid' => ['hybrid', 'Benzin'],
            'generic diesel hybrid' => ['hybrid', 'Diesel'],
        ];
    }

    /**
     * @dataProvider combustionFuelTypes
     */
    public function testCombustionVehicleStillClearsInvalidElectricValues(?string $fuelType, ?string $fuel): void
    {
        $ecoData = $this->createEcoData();
        $ecoData->fuelType = $fuelType;
        $ecoData->fuel = $fuel;
        $ecoData->combinedFuelConsumptionMax = 6.0;
        $ecoData->combinedPowerConsumptionMin = 14.0;
        $ecoData->combinedPowerConsumptionMax = 15.1;
        $ecoData->combinedPowerConsumptionWeightedMin = 12.0;
        $ecoData->combinedPowerConsumptionWeightedMax = 13.0;
        $ecoData->rangeMin = 80;
        $ecoData->rangeMax = 90;

        $ecoData->removeInvalidValues();

        self::assertNull($ecoData->combinedPowerConsumptionMin);
        self::assertNull($ecoData->combinedPowerConsumptionMax);
        self::assertNull($ecoData->combinedPowerConsumptionWeightedMin);
        self::assertNull($ecoData->combinedPowerConsumptionWeightedMax);
        self::assertNull($ecoData->rangeMin);
        self::assertNull($ecoData->rangeMax);
        self::assertSame(6.0, $ecoData->combinedFuelConsumptionMax);
    }

    public function combustionFuelTypes(): array
    {
        return [
            'petrol drive' => ['petrol', null],
            'diesel drive' => ['diesel', null],
            'petrol label fallback' => [null, 'Benzin'],
            'diesel label fallback' => [null, 'Diesel'],
        ];
    }

    public function testElectricVehicleStillClearsInvalidFuelValues(): void
    {
        $ecoData = $this->createEcoData();
        $ecoData->fuelType = 'electricity';
        $ecoData->fuel = 'Elektro';
        $ecoData->combinedFuelConsumptionMax = 6.0;
        $ecoData->combinedFuelConsumptionWeightedMax = 2.4;
        $ecoData->co2EmissionMax = 140.0;
        $ecoData->co2EmissionWeightedMax = 54.0;
        $ecoData->cubicCapacity = 1499;
        $ecoData->combinedPowerConsumptionMax = 15.1;
        $ecoData->rangeMax = 90;

        $ecoData->removeInvalidValues();

        self::assertNull($ecoData->combinedFuelConsumptionMax);
        self::assertNull($ecoData->combinedFuelConsumptionWeightedMax);
        self::assertNull($ecoData->co2EmissionMax);
        self::assertNull($ecoData->co2EmissionWeightedMax);
        self::assertNull($ecoData->cubicCapacity);
        self::assertNull($ecoData->fuel);
        self::assertSame(15.1, $ecoData->combinedPowerConsumptionMax);
        self::assertSame(90, $ecoData->rangeMax);
    }

    private function createEcoData(): EcoData
    {
        $translator = new Translator('de');
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addResource('yaml', dirname(__DIR__, 3).'/Resources/translations/messages.de.yaml', 'de');

        return new EcoData($translator);
    }
}
