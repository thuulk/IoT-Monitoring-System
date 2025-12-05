#pragma once
#include <Arduino.h>
#include <cstring>

// ===== Variables globales de setpoints por target =====
// Declaración (se definen en main.cpp)
extern int sp_temp_min, sp_temp_max;
extern int sp_hum_min,  sp_hum_max;
extern int sp_pres_min, sp_pres_max;
extern int sp_co2_min,  sp_co2_max;
extern int sp_tvoc_min, sp_tvoc_max;
extern int sp_pm1_min,  sp_pm1_max;

// ===== Helpers =====

// Actualiza los setpoints según el nombre del target.
// Regresa true si el target fue reconocido.
inline bool updateSetpointByTarget(const char* target, int minVal, int maxVal) {
    if (!target) return false;

    if (strcmp(target, "temperatura") == 0) {
        sp_temp_min = minVal; sp_temp_max = maxVal; return true;
    }
    if (strcmp(target, "humedad") == 0) {
        sp_hum_min  = minVal; sp_hum_max  = maxVal; return true;
    }
    if (strcmp(target, "presion") == 0) {
        sp_pres_min = minVal; sp_pres_max = maxVal; return true;
    }
    if (strcmp(target, "co2") == 0) {
        sp_co2_min  = minVal; sp_co2_max  = maxVal; return true;
    }
    if (strcmp(target, "tvoc") == 0) {
        sp_tvoc_min = minVal; sp_tvoc_max = maxVal; return true;
    }
    if (strcmp(target, "pm1") == 0) {
        sp_pm1_min  = minVal; sp_pm1_max  = maxVal; return true;
    }

    return false;
}

// Obtiene punteros a min/max para un target.
// Útil si quieres lógica más genérica.
inline bool getSetpointPtrs(const char* target, int*& outMin, int*& outMax) {
    if (!target) return false;

    if (strcmp(target, "temperatura") == 0) { outMin = &sp_temp_min; outMax = &sp_temp_max; return true; }
    if (strcmp(target, "humedad") == 0)     { outMin = &sp_hum_min;  outMax = &sp_hum_max;  return true; }
    if (strcmp(target, "presion") == 0)     { outMin = &sp_pres_min; outMax = &sp_pres_max; return true; }
    if (strcmp(target, "co2") == 0)         { outMin = &sp_co2_min;  outMax = &sp_co2_max;  return true; }
    if (strcmp(target, "tvoc") == 0)        { outMin = &sp_tvoc_min; outMax = &sp_tvoc_max; return true; }
    if (strcmp(target, "pm1") == 0)         { outMin = &sp_pm1_min;  outMax = &sp_pm1_max;  return true; }

    return false;
}

// Evalúa si un valor está fuera de rango para un target.
// Si min o max están en -1, los considera "no configurados".
inline bool isOutOfRange(const char* target, float value) {
    int* pMin = nullptr;
    int* pMax = nullptr;
    if (!getSetpointPtrs(target, pMin, pMax)) return false;

    const int minVal = *pMin;
    const int maxVal = *pMax;

    if (minVal != -1 && value < minVal) return true;
    if (maxVal != -1 && value > maxVal) return true;

    return false;
}
