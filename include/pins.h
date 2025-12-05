#pragma once
#include <Arduino.h>

namespace pins {

    // I2C compartido
    inline constexpr uint8_t I2C_SDA = D2;
    inline constexpr uint8_t I2C_SCL = D1;

    // UART PMS5003 (ejemplo, ajusta si ya los tienes definidos)
    inline constexpr uint8_t PMS_RX  = D5; // ESP recibe
    inline constexpr uint8_t PMS_TX  = D6; // ESP envía

    // Otros
    inline constexpr uint8_t BUZZER  = D7;

} // namespace pins