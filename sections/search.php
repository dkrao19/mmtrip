<style>
/* Scoped Styles for Search Module */
.search-container {
    max-width: 1140px;
    margin: -100px auto 30px; /* Overlap Hero */
    position: relative;
    z-index: 20;
    padding: 0 15px;
}
.search-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    padding: 25px 30px;
}

/* Tabs */
.search-tabs {
    display: flex;
    gap: 20px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 15px;
    margin-bottom: 25px;
    overflow-x: auto;
}
.tab-item {
    font-size: 15px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}
.tab-item:hover { background: #f1f5f9; color: #334155; }
.tab-item.active {
    background: #eff6ff;
    color: var(--primary, #0a2d4d);
    font-weight: 700;
}

/* Trip Type Radio Pills */
.trip-type-row {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}
.trip-radio {
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #475569;
}
.trip-radio input { accent-color: var(--secondary, #ff7a00); cursor: pointer; }

/* Main Grid */
.search-grid {
    display: grid;
    grid-template-columns: 2fr 2fr 1.2fr 1.2fr 1.5fr auto;
    gap: 12px;
    align-items: center;
}

/* Input Boxes */
.input-box {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    height: 54px;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 0 12px;
    background: #fff;
    transition: border 0.2s;
}
.input-box:focus-within {
    border-color: var(--primary, #0a2d4d);
    box-shadow: 0 0 0 3px rgba(10, 45, 77, 0.1);
}
.input-label {
    font-size: 11px;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 700;
    margin-bottom: 2px;
}
.input-field {
    border: none;
    outline: none;
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    width: 100%;
    background: transparent;
    padding: 0;
}
.input-field:disabled { background: transparent; color: #cbd5e1; cursor: not-allowed; }

/* Autocomplete Suggestion Box */
.suggest-dropdown {
    position: absolute;
    top: 100%; left: 0; right: 0;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    z-index: 100;
    max-height: 300px;
    overflow-y: auto;
    margin-top: 5px;
    display: none; /* JS toggles this */
}

/* Passenger Popup */
.pax-wrapper { position: relative; }
.pax-dropdown {
    display: none; /* JS toggles this */
    position: absolute;
    top: 60px; left: 0;
    width: 280px;
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    z-index: 101;
    border: 1px solid #e2e8f0;
}
.pax-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    font-weight: 600;
    color: #334155;
}
.qty-btn {
    width: 30px; height: 30px;
    border-radius: 50%;
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #64748b;
    cursor: pointer;
    font-weight: 700;
}
.qty-btn:hover { border-color: var(--primary, #0a2d4d); color: var(--primary, #0a2d4d); }
.qty-val { width: 20px; display: inline-block; text-align: center; }

/* Search Button */
.btn-search-main {
    height: 54px;
    padding: 0 30px;
    background: var(--secondary, #ff7a00);
    color: #fff;
    font-weight: 700;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.1s;
}
.btn-search-main:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 122, 0, 0.3); }

/* Responsive */
@media(max-width: 900px) {
    .search-grid { grid-template-columns: 1fr 1fr; }
    .btn-search-main { grid-column: span 2; }
}
@media(max-width: 600px) {
    .search-grid { grid-template-columns: 1fr; }
    .btn-search-main { grid-column: span 1; }
    .search-tabs { overflow-x: scroll; white-space: nowrap; }
}
</style>

<div class="search-container">
    <div class="search-card">

        <div class="search-tabs">
            <?php if(!empty($settings['show_flights'])): ?>
                <div class="tab-item active"><i class="fas fa-plane"></i> Flights</div>
            <?php endif; ?>
            <?php if(!empty($settings['show_hotels'])): ?>
                <div class="tab-item"><i class="fas fa-hotel"></i> Hotels</div>
            <?php endif; ?>
            <?php if(!empty($settings['show_bus'])): ?>
                <div class="tab-item"><i class="fas fa-bus"></i> Bus</div>
            <?php endif; ?>
            <?php if(!empty($settings['show_insurance'])): ?>
                <div class="tab-item"><i class="fas fa-user-shield"></i> Insurance</div>
            <?php endif; ?>
            <?php if(!empty($settings['show_packages'])): ?>
                <div class="tab-item"><i class="fas fa-umbrella-beach"></i> Packages</div>
            <?php endif; ?>
        </div>

        <div id="flightForm">
            
            <div class="trip-type-row">
                <label class="trip-radio">
                    <input type="radio" name="trip" value="ONEWAY" checked> 
                    <span>One Way</span>
                </label>
                <label class="trip-radio">
                    <input type="radio" name="trip" value="RETURN"> 
                    <span>Round Trip</span>
                </label>
            </div>

            <div class="search-grid">
                
                <div class="input-box">
                    <span class="input-label">From</span>
                    <input class="input-field" id="from" placeholder="Origin City" autocomplete="off">
                    <div class="suggest-dropdown" id="fromSug"></div>
                </div>

                <div class="input-box">
                    <span class="input-label">To</span>
                    <input class="input-field" id="to" placeholder="Destination City" autocomplete="off">
                    <div class="suggest-dropdown" id="toSug"></div>
                </div>

                <div class="input-box">
                    <span class="input-label">Departure</span>
                    <input type="date" class="input-field" id="depart">
                </div>

                <div class="input-box">
                    <span class="input-label">Return</span>
                    <input type="date" class="input-field" id="return" disabled>
                </div>

                <div class="pax-wrapper">
                    <div class="input-box" onclick="document.getElementById('paxPop').style.display='block'" style="cursor:pointer">
                        <span class="input-label">Travellers & Class</span>
                        <input class="input-field" id="paxText" readonly value="1 Adult" style="cursor:pointer">
                        <i class="fas fa-chevron-down" style="position:absolute; right:10px; font-size:12px; color:#94a3b8;"></i>
                    </div>

                    <div class="pax-dropdown" id="paxPop">
                        <div class="pax-row">
                            <span>Adults <small style="display:block;font-weight:400;color:#94a3b8">(12+ yrs)</small></span>
                            <div>
                                <button class="qty-btn" onclick="chg('a',-1)">-</button>
                                <span class="qty-val" id="a">1</span>
                                <button class="qty-btn" onclick="chg('a',1)">+</button>
                            </div>
                        </div>
                        <div class="pax-row">
                            <span>Children <small style="display:block;font-weight:400;color:#94a3b8">(2-12 yrs)</small></span>
                            <div>
                                <button class="qty-btn" onclick="chg('c',-1)">-</button>
                                <span class="qty-val" id="c">0</span>
                                <button class="qty-btn" onclick="chg('c',1)">+</button>
                            </div>
                        </div>
                        <div class="pax-row">
                            <span>Infants <small style="display:block;font-weight:400;color:#94a3b8">(0-2 yrs)</small></span>
                            <div>
                                <button class="qty-btn" onclick="chg('i',-1)">-</button>
                                <span class="qty-val" id="i">0</span>
                                <button class="qty-btn" onclick="chg('i',1)">+</button>
                            </div>
                        </div>
                        <button onclick="document.getElementById('paxPop').style.display='none'" 
                                style="width:100%; padding:10px; background:var(--primary, #0a2d4d); color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:600;">
                            Done
                        </button>
                    </div>
                </div>

                <button class="btn-search-main" onclick="searchFlights()">
                    SEARCH
                </button>

            </div> </div>

    </div>
</div>