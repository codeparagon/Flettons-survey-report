{{-- Client/Property Tab Content - Simplified UK Property Survey --}}
<div class="survey-tab-content">
    {{-- Content Header - Sophisticated Design --}}
    <div class="survey-content-header">
        <h2 class="survey-tab-title">Client & Property Information</h2>
    </div>
    
    {{-- Simple Input Fields --}}
    <div class="survey-form-section">
        <div class="survey-form-grid">
            <div class="survey-form-group">
                <label class="survey-form-label">Client Name</label>
                <input type="text" class="survey-form-input" name="client_name" 
                       value="{{ old('client_name', $survey->client_name ?? '') }}" 
                       placeholder="Enter client name">
            </div>
            
            <div class="survey-form-group">
                <label class="survey-form-label">Address</label>
                <input type="text" class="survey-form-input" name="address" 
                       value="{{ old('address', $survey->full_address ?? '') }}" 
                       placeholder="Enter property address">
            </div>
            
            <div class="survey-form-group">
                <label class="survey-form-label">Local Authority</label>
                <div class="survey-select-wrapper">
                    <select class="survey-select-dropdown" name="local_authority" id="local_authority_select">
                        <option value="">Please specify</option>
                        <option value="Lewisham London Borough Council">Lewisham London Borough Council</option>
                        <option value="Ashford Borough Council">Ashford Borough Council</option>
                        <option value="Barking and Dagenham London Borough Council">Barking and Dagenham London Borough Council</option>
                        <option value="Barnet London Borough Council">Barnet London Borough Council</option>
                        <option value="Basingstoke and Deane Borough Council">Basingstoke and Deane Borough Council</option>
                        <option value="Bexley London Borough Council">Bexley London Borough Council</option>
                        <option value="Brent London Borough Council">Brent London Borough Council</option>
                        <option value="Bromley London Borough Council">Bromley London Borough Council</option>
                        <option value="Camden London Borough Council">Camden London Borough Council</option>
                        <option value="Canterbury City Council">Canterbury City Council</option>
                        <option value="Croydon London Borough Council">Croydon London Borough Council</option>
                        <option value="Dartford Borough Council">Dartford Borough Council</option>
                        <option value="Dover District Council">Dover District Council</option>
                        <option value="Ealing London Borough Council">Ealing London Borough Council</option>
                        <option value="East Hampshire District Council">East Hampshire District Council</option>
                        <option value="Eastleigh Borough Council">Eastleigh Borough Council</option>
                        <option value="Elmbridge Borough Council">Elmbridge Borough Council</option>
                        <option value="Enfield London Borough Council">Enfield London Borough Council</option>
                        <option value="Epping Forest Council">Epping Forest Council</option>
                        <option value="Epsom and Ewell Borough Council">Epsom and Ewell Borough Council</option>
                        <option value="Fareham Borough Council">Fareham Borough Council</option>
                        <option value="Gosport Borough Council">Gosport Borough Council</option>
                        <option value="Gravesham Borough Council">Gravesham Borough Council</option>
                        <option value="Greenwich London Borough Council">Greenwich London Borough Council</option>
                        <option value="Guildford Borough Council">Guildford Borough Council</option>
                        <option value="Hackney London Borough Council">Hackney London Borough Council</option>
                        <option value="Hammersmith and Fulham London Borough Council">Hammersmith and Fulham London Borough Council</option>
                        <option value="Haringey London Borough Council">Haringey London Borough Council</option>
                        <option value="Harrow London Borough Council">Harrow London Borough Council</option>
                        <option value="Hart District Council">Hart District Council</option>
                        <option value="Havant Borough Council">Havant Borough Council</option>
                        <option value="Havering London Borough Council">Havering London Borough Council</option>
                        <option value="Hillingdon London Borough Council">Hillingdon London Borough Council</option>
                        <option value="Hounslow London Borough Council">Hounslow London Borough Council</option>
                        <option value="Islington London Borough Council">Islington London Borough Council</option>
                        <option value="Kensington and Chelsea London Borough Council">Kensington and Chelsea London Borough Council</option>
                        <option value="Kingston upon Thames London Borough Council">Kingston upon Thames London Borough Council</option>
                        <option value="Lambeth London Borough Council">Lambeth London Borough Council</option>
                        <option value="Lewisham London Borough Council">Lewisham London Borough Council</option>
                        <option value="Maidstone Borough Council">Maidstone Borough Council</option>
                        <option value="Medway Council">Medway Council</option>
                        <option value="Merton London Borough Council">Merton London Borough Council</option>
                        <option value="Mole Valley Borough Council">Mole Valley Borough Council</option>
                        <option value="New Forest District Council">New Forest District Council</option>
                        <option value="Newham London Borough Council">Newham London Borough Council</option>
                        <option value="Redbridge London Borough Council">Redbridge London Borough Council</option>
                        <option value="Reigate and Banstead Borough Council">Reigate and Banstead Borough Council</option>
                        <option value="Richmond upon Thames London Borough Council">Richmond upon Thames London Borough Council</option>
                        <option value="Runnymede Borough Council">Runnymede Borough Council</option>
                        <option value="Rushmoor Borough Council">Rushmoor Borough Council</option>
                        <option value="Sevenoaks District Council">Sevenoaks District Council</option>
                        <option value="Shepway District Council">Shepway District Council</option>
                        <option value="Southwark London Borough Council">Southwark London Borough Council</option>
                        <option value="Spelthorne Borough Council">Spelthorne Borough Council</option>
                        <option value="Surrey Heath Borough Council">Surrey Heath Borough Council</option>
                        <option value="Sutton London Borough Council">Sutton London Borough Council</option>
                        <option value="Swale Borough Council">Swale Borough Council</option>
                        <option value="Tandridge Borough Council">Tandridge Borough Council</option>
                        <option value="Test Valley Borough Council">Test Valley Borough Council</option>
                        <option value="Thanet District Council">Thanet District Council</option>
                        <option value="Tonbridge and Malling Borough Council">Tonbridge and Malling Borough Council</option>
                        <option value="Tower Hamlets London Borough Council">Tower Hamlets London Borough Council</option>
                        <option value="Tunbridge Wells Borough Council">Tunbridge Wells Borough Council</option>
                        <option value="Waltham Forest London Borough Council">Waltham Forest London Borough Council</option>
                        <option value="Wandsworth London Borough Council">Wandsworth London Borough Council</option>
                        <option value="Waverley Borough Council">Waverley Borough Council</option>
                        <option value="Westminster City Council">Westminster City Council</option>
                        <option value="Winchester City Council">Winchester City Council</option>
                        <option value="Woking Borough Council">Woking Borough Council</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Button-Based Selection Fields --}}
    <div class="survey-property-details">
        <div class="survey-property-section">
            <div class="survey-property-title">Estate Holding</div>
            <div class="survey-button-group">
                <button type="button" class="survey-button estate-holding-btn" data-value="Freehold">Freehold</button>
                <button type="button" class="survey-button estate-holding-btn" data-value="Leasehold">Leasehold</button>
            </div>
            <input type="hidden" name="estate_holding" id="estate_holding" value="">
        </div>
        
        <div class="survey-property-section">
            <div class="survey-property-title">Council Tax Band</div>
            <div class="survey-button-group">
                <button type="button" class="survey-button council-tax-btn" data-value="A">A</button>
                <button type="button" class="survey-button council-tax-btn" data-value="B">B</button>
                <button type="button" class="survey-button council-tax-btn" data-value="C">C</button>
                <button type="button" class="survey-button council-tax-btn" data-value="D">D</button>
                <button type="button" class="survey-button council-tax-btn" data-value="E">E</button>
                <button type="button" class="survey-button council-tax-btn" data-value="F">F</button>
                <button type="button" class="survey-button council-tax-btn" data-value="G">G</button>
                <button type="button" class="survey-button council-tax-btn" data-value="H">H</button>
            </div>
            <input type="hidden" name="council_tax_band" id="council_tax_band" value="">
        </div>
        
        <div class="survey-property-section">
            <div class="survey-property-title">EPC Rating</div>
            <div class="survey-button-group">
                <button type="button" class="survey-button epc-rating-btn" data-value="A">A</button>
                <button type="button" class="survey-button epc-rating-btn" data-value="B">B</button>
                <button type="button" class="survey-button epc-rating-btn" data-value="C">C</button>
                <button type="button" class="survey-button epc-rating-btn" data-value="D">D</button>
                <button type="button" class="survey-button epc-rating-btn" data-value="E">E</button>
                <button type="button" class="survey-button epc-rating-btn" data-value="F">F</button>
                <button type="button" class="survey-button epc-rating-btn" data-value="G">G</button>
                <button type="button" class="survey-button epc-rating-btn" data-value="N/A">N/A</button>
            </div>
            <input type="hidden" name="epc_rating" id="epc_rating" value="">
        </div>
        
        <div class="survey-property-section">
            <div class="survey-property-title">Flood Risk</div>
            <div class="survey-button-group">
                <button type="button" class="survey-button flood-risk-btn" data-value="Rivers and Seas">Rivers and Seas</button>
            </div>
            <div style="margin-top: 0.75rem; display: flex; align-items: center;">
                <span style="font-weight: 600; color: #1A202C;">SURFACE WATER</span>
                <i class="fas fa-check-circle survey-check-icon" id="surface-water-check" style="display: none; margin-left: 0.5rem;"></i>
            </div>
            <input type="hidden" name="flood_risk" id="flood_risk" value="">
        </div>
        
        <div class="survey-property-section">
            <div class="survey-property-title">Reservoirs</div>
            <div class="survey-button-group">
                <button type="button" class="survey-button reservoirs-btn" data-value="Ground Water">Ground Water</button>
            </div>
            <div style="margin-top: 0.75rem;">
                <span style="font-weight: 600; color: #1A202C;">GROUND WATER</span>
            </div>
            <input type="hidden" name="reservoirs" id="reservoirs" value="">
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Button group handlers - simple toggle
    function setupButtonGroup(buttons, hiddenInput, checkIcon = null) {
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                buttons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                hiddenInput.value = this.dataset.value;
                if (checkIcon) {
                    checkIcon.style.display = 'inline-block';
                }
            });
        });
    }
    
    // Estate Holding
    const estateHoldingBtns = document.querySelectorAll('.estate-holding-btn');
    const estateHoldingInput = document.getElementById('estate_holding');
    if (estateHoldingBtns.length && estateHoldingInput) {
        setupButtonGroup(estateHoldingBtns, estateHoldingInput);
    }
    
    // Council Tax Band
    const councilTaxBtns = document.querySelectorAll('.council-tax-btn');
    const councilTaxInput = document.getElementById('council_tax_band');
    if (councilTaxBtns.length && councilTaxInput) {
        setupButtonGroup(councilTaxBtns, councilTaxInput);
    }
    
    // EPC Rating
    const epcRatingBtns = document.querySelectorAll('.epc-rating-btn');
    const epcRatingInput = document.getElementById('epc_rating');
    if (epcRatingBtns.length && epcRatingInput) {
        setupButtonGroup(epcRatingBtns, epcRatingInput);
    }
    
    // Flood Risk
    const floodRiskBtns = document.querySelectorAll('.flood-risk-btn');
    const floodRiskInput = document.getElementById('flood_risk');
    const surfaceWaterCheck = document.getElementById('surface-water-check');
    if (floodRiskBtns.length && floodRiskInput) {
        setupButtonGroup(floodRiskBtns, floodRiskInput, surfaceWaterCheck);
    }
    
    // Reservoirs
    const reservoirsBtns = document.querySelectorAll('.reservoirs-btn');
    const reservoirsInput = document.getElementById('reservoirs');
    if (reservoirsBtns.length && reservoirsInput) {
        setupButtonGroup(reservoirsBtns, reservoirsInput);
    }
});
</script>
@endpush
