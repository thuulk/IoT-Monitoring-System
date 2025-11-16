from fastapi import FastAPI
from pydantic import BaseModel
from datetime import datetime
import sqlite3

app = FastAPI()
DB="readings.db"

class Reading(BaseModel):
    device_id:str; temp_c:float; hum_pct:float; press_hpa:float
    ts:int|None=None

def init_db():
    con=sqlite3.connect(DB)
    con.execute("""CREATE TABLE IF NOT EXISTS readings(
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      device_id TEXT, temp_c REAL, hum_pct REAL, press_hpa REAL,
      ts_client INTEGER, ts_server TEXT
    )""")
    con.commit(); con.close()

@app.on_event("startup")
def _s(): init_db()

@app.post("/api/readings")
def create(r: Reading):
    con=sqlite3.connect(DB)
    con.execute("INSERT INTO readings(device_id,temp_c,hum_pct,press_hpa,ts_client,ts_server) VALUES (?,?,?,?,?,?)",
                (r.device_id,r.temp_c,r.hum_pct,r.press_hpa,r.ts or 0, datetime.utcnow().isoformat()))
    con.commit(); con.close()
    return {"ok": True}
