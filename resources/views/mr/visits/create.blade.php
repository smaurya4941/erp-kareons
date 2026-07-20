@extends('layouts.app')

@section('content')
<div x-data="visitWizard({{ $products->toJson() }}, {{ $assignedSamples->toJson() }})">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">New Doctor Visit</h2>
            <p class="text-sm text-gray-500">Step <span x-text="step"></span> of 6</p>
        </div>
        <div class="text-sm font-medium text-gray-400" x-show="step < 6">
            <button @click="step--" x-show="step > 1" class="text-blue-600 hover:underline mr-4">Previous Step</button>
        </div>
    </div>

    <!-- Error Messages -->
    <div x-show="errorMessage" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" style="display: none;">
        <p x-text="errorMessage"></p>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" :style="'width: ' + ((step / 6) * 100) + '%'"></div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 max-w-4xl mx-auto relative min-h-[400px]">

        <!-- STEP 1: Location & Doctor Details -->
        <div x-show="step === 1" x-transition.opacity>
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Step 1: Location & Doctor Details</h3>
            
            <div class="bg-blue-50 p-3 rounded border border-blue-100 mb-6 flex items-start">
                <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <div>
                    <p class="text-sm font-semibold text-blue-800" x-text="locationStatus"></p>
                    <p class="text-xs text-blue-600 mt-1" x-show="form.lat">Lat: <span x-text="form.lat"></span>, Lng: <span x-text="form.lng"></span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Doctor Name *</label>
                    <input type="text" x-model="form.doctor_name" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" placeholder="e.g. Dr. Rajesh Sharma">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Specialization *</label>
                    <select x-model="form.specialization" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 bg-white">
                        <option value="">Select Specialization</option>
                        <option value="General Physician">General Physician</option>
                        <option value="Orthopedic">Orthopedic</option>
                        <option value="Pediatrician">Pediatrician</option>
                        <option value="Gynecologist">Gynecologist</option>
                        <option value="Dermatologist">Dermatologist</option>
                        <option value="ENT">ENT</option>
                        <option value="Cardiologist">Cardiologist</option>
                        <option value="Neurologist">Neurologist</option>
                        <option value="Dentist">Dentist</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Clinic / Hospital Name</label>
                    <input type="text" x-model="form.clinic_name" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" placeholder="Optional">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Area</label>
                    <input type="text" x-model="form.area" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" placeholder="e.g. Gomti Nagar">
                </div>
            </div>
            <div class="mt-8 flex flex-col sm:flex-row sm:justify-end">
                <x-button type="button" variant="primary" @click="validateStep1()" class="w-full sm:w-auto justify-center">Next Step →</x-button>
            </div>
        </div>

        <!-- STEP 2: Discussion -->
        <div x-show="step === 2" x-transition.opacity style="display: none;">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Step 2: Visit Discussion</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Doctor's Overall Response *</label>
                    <div class="mt-2 flex flex-wrap gap-3">
                        <label class="border rounded px-4 py-2 cursor-pointer hover:bg-gray-50 flex items-center" :class="form.doctor_response === 'Interested' ? 'bg-blue-50 border-blue-500' : ''">
                            <input type="radio" x-model="form.doctor_response" value="Interested" class="hidden">
                            <span class="text-sm" :class="form.doctor_response === 'Interested' ? 'text-blue-700 font-bold' : 'text-gray-700'">Interested</span>
                        </label>
                        <label class="border rounded px-4 py-2 cursor-pointer hover:bg-gray-50 flex items-center" :class="form.doctor_response === 'Not Interested' ? 'bg-red-50 border-red-500' : ''">
                            <input type="radio" x-model="form.doctor_response" value="Not Interested" class="hidden">
                            <span class="text-sm" :class="form.doctor_response === 'Not Interested' ? 'text-red-700 font-bold' : 'text-gray-700'">Not Interested</span>
                        </label>
                        <label class="border rounded px-4 py-2 cursor-pointer hover:bg-gray-50 flex items-center" :class="form.doctor_response === 'Will Think' ? 'bg-yellow-50 border-yellow-500' : ''">
                            <input type="radio" x-model="form.doctor_response" value="Will Think" class="hidden">
                            <span class="text-sm" :class="form.doctor_response === 'Will Think' ? 'text-yellow-700 font-bold' : 'text-gray-700'">Will Think</span>
                        </label>
                        <label class="border rounded px-4 py-2 cursor-pointer hover:bg-gray-50 flex items-center" :class="form.doctor_response === 'Requested Follow-up' ? 'bg-green-50 border-green-500' : ''">
                            <input type="radio" x-model="form.doctor_response" value="Requested Follow-up" class="hidden">
                            <span class="text-sm" :class="form.doctor_response === 'Requested Follow-up' ? 'text-green-700 font-bold' : 'text-gray-700'">Requested Follow-up</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Discussion Summary *</label>
                    <textarea x-model="form.discussion_summary" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" rows="3" placeholder="What was discussed?"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Competitor Medicines Mentioned</label>
                    <input type="text" x-model="form.competitor_medicines" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" placeholder="e.g. Dabur, Patanjali">
                </div>
            </div>
            
            <div class="mt-8 flex flex-col sm:flex-row sm:justify-end">
                <x-button type="button" variant="primary" @click="validateStep2()" class="w-full sm:w-auto justify-center">Next Step →</x-button>
            </div>
        </div>

        <!-- STEP 3: Products Discussed -->
        <div x-show="step === 3" x-transition.opacity style="display: none;">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4 flex justify-between items-center">
                Step 3: Products Discussed
                <button type="button" @click="addProductRow()" class="text-sm text-blue-600 hover:text-blue-800 flex items-center font-medium">
                    + Add Product
                </button>
            </h3>
            
            <div class="space-y-4 mb-4">
                <template x-for="(row, index) in form.products" :key="index">
                    <div class="p-4 border rounded bg-gray-50 relative">
                        <button type="button" @click="removeProductRow(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 p-1" x-show="form.products.length > 1" title="Remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Select Product *</label>
                                <select x-model="row.product_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm bg-white">
                                    <option value="">-- Choose Product --</option>
                                    <template x-for="p in allProducts" :key="p.id">
                                        <option :value="p.id" x-text="p.name"></option>
                                    </template>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Interest Level *</label>
                                <select x-model="row.interest_level" class="block w-full text-sm border-gray-300 rounded-md shadow-sm bg-white">
                                    <option value="">-- Select Interest --</option>
                                    <option value="Very High">Very High</option>
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                    <option value="Not Interested">Not Interested</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Remarks</label>
                                <input type="text" x-model="row.remarks" class="block w-full text-sm border-gray-300 rounded-md shadow-sm" placeholder="Product specific feedback from doctor...">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="mt-8 flex flex-col sm:flex-row sm:justify-end">
                <x-button type="button" variant="primary" @click="validateStep3()" class="w-full sm:w-auto justify-center">Next Step →</x-button>
            </div>
        </div>

        <!-- STEP 4: Samples Distributed -->
        <div x-show="step === 4" x-transition.opacity style="display: none;">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4 flex justify-between items-center">
                Step 4: Samples Given (Optional)
                <button type="button" @click="addSampleRow()" class="text-sm text-green-600 hover:text-green-800 flex items-center font-medium">
                    + Add Sample
                </button>
            </h3>
            
            <p class="text-sm text-gray-500 mb-4">Only products assigned to you with remaining stock are shown.</p>

            <div class="space-y-4 mb-4">
                <template x-for="(row, index) in form.samples" :key="index">
                    <div class="p-4 border rounded-xl bg-green-50 relative flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-end">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Select Sample</label>
                            <select x-model="row.product_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm bg-white">
                                <option value="">-- Choose Sample --</option>
                                <template x-for="s in assignedSamples" :key="s.product_id">
                                    <option :value="s.product_id" x-text="s.product_name + ' (Remaining: ' + s.remaining_quantity + ')'"></option>
                                </template>
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <div class="flex-1 sm:w-32">
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Quantity</label>
                                <input type="number" x-model="row.quantity" min="1" class="block w-full text-sm border-gray-300 rounded-md shadow-sm">
                            </div>
                            <button type="button" @click="removeSampleRow(index)" class="text-red-500 hover:text-red-700 p-2 flex-shrink-0" title="Remove">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
                
                <div x-show="form.samples.length === 0" class="text-center py-6 border-2 border-dashed border-gray-200 rounded">
                    <p class="text-gray-500 text-sm">No samples given.</p>
                </div>
            </div>
            
            <div class="mt-8 flex flex-col sm:flex-row sm:justify-end">
                <x-button type="button" variant="primary" @click="validateStep4()" class="w-full sm:w-auto justify-center">Next Step →</x-button>
            </div>
        </div>

        <!-- STEP 5: Order Collection -->
        <div x-show="step === 5" x-transition.opacity style="display: none;">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4 flex justify-between items-center">
                Step 5: Order Collection (Optional)
                <button type="button" @click="addOrderRow()" class="text-sm text-yellow-600 hover:text-yellow-800 flex items-center font-medium">
                    + Add Order Item
                </button>
            </h3>
            
            <div class="space-y-4 mb-4">
                <template x-for="(row, index) in form.orders" :key="index">
                    <div class="p-4 border rounded-xl bg-yellow-50 relative flex flex-col sm:flex-row gap-3 sm:gap-4 sm:items-end">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Select Product</label>
                            <select x-model="row.product_id" class="block w-full text-sm border-gray-300 rounded-md shadow-sm bg-white">
                                <option value="">-- Choose Product --</option>
                                <template x-for="p in allProducts" :key="p.id">
                                    <option :value="p.id" x-text="p.name"></option>
                                </template>
                            </select>
                        </div>

                        <div class="flex items-end gap-3">
                            <div class="flex-1 sm:w-32">
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Quantity</label>
                                <input type="number" x-model="row.quantity" min="1" class="block w-full text-sm border-gray-300 rounded-md shadow-sm">
                            </div>
                            <button type="button" @click="removeOrderRow(index)" class="text-red-500 hover:text-red-700 p-2 flex-shrink-0" title="Remove">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
                
                <div x-show="form.orders.length === 0" class="text-center py-6 border-2 border-dashed border-gray-200 rounded">
                    <p class="text-gray-500 text-sm">No orders collected.</p>
                </div>
            </div>

            <!-- Order Remarks -->
            <div class="mb-4 pt-4 border-t" x-show="form.orders.length > 0">
                <label class="block text-sm font-bold text-gray-700 mb-2">Order Remarks (Optional)</label>
                <textarea x-model="form.order_remarks" class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" rows="2" placeholder="e.g. Urgent requirement, deliver next week..."></textarea>
            </div>
            
            <div class="mt-8 flex flex-col sm:flex-row sm:justify-end">
                <x-button type="button" variant="primary" @click="validateStep5()" class="w-full sm:w-auto justify-center">Review & Submit →</x-button>
            </div>
        </div>

        <!-- STEP 6: Review -->
        <div x-show="step === 6" x-transition.opacity style="display: none;">
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Step 6: Review & Submit</h3>
            
            <div class="space-y-6">
                <!-- Summary Card -->
                <div class="bg-gray-50 p-4 rounded-xl border text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="font-bold text-gray-500 uppercase text-xs">Doctor</span>
                            <p class="font-bold text-lg text-gray-800" x-text="form.doctor_name"></p>
                            <p class="text-gray-600" x-text="form.specialization"></p>
                        </div>
                        <div>
                            <span class="font-bold text-gray-500 uppercase text-xs">Response</span>
                            <p class="font-bold text-gray-800" x-text="form.doctor_response"></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="font-bold text-gray-500 uppercase text-xs">Discussion Summary</span>
                        <p class="text-gray-700" x-text="form.discussion_summary"></p>
                    </div>
                </div>

                <!-- Products -->
                <div>
                    <h4 class="font-bold text-gray-700 border-b mb-2">Products Discussed (<span x-text="form.products.length"></span>)</h4>
                    <ul class="list-disc pl-5 text-sm text-gray-600">
                        <template x-for="p in form.products">
                            <li x-text="getProductName(p.product_id) + ' - ' + p.interest_level"></li>
                        </template>
                    </ul>
                </div>

                <!-- Samples & Orders -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-bold text-gray-700 border-b mb-2 text-green-700">Samples Given</h4>
                        <ul class="list-disc pl-5 text-sm text-gray-600">
                            <template x-for="s in form.samples">
                                <li x-text="getProductName(s.product_id) + ' x ' + s.quantity"></li>
                            </template>
                            <li x-show="form.samples.length === 0">None</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-700 border-b mb-2 text-yellow-700">Orders Collected</h4>
                        <ul class="list-disc pl-5 text-sm text-gray-600">
                            <template x-for="o in form.orders">
                                <li x-text="getProductName(o.product_id) + ' x ' + o.quantity"></li>
                            </template>
                            <li x-show="form.orders.length === 0">None</li>
                        </ul>
                        <div x-show="form.order_remarks && form.orders.length > 0" class="mt-2 bg-yellow-50 p-2 rounded text-xs text-gray-700">
                            <strong>Remarks:</strong> <span x-text="form.order_remarks"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex flex-col sm:flex-row sm:justify-end">
                <x-button type="button" id="submit-btn" class="bg-green-600 hover:bg-green-700 text-white w-full sm:w-auto justify-center" @click="submitVisit()">Submit Visit</x-button>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('visitWizard', (products, assignedSamples) => ({
            step: 1,
            allProducts: products,
            assignedSamples: assignedSamples,
            errorMessage: '',
            locationStatus: 'Fetching GPS...',
            
            form: {
                lat: null,
                lng: null,
                accuracy: null,
                address: null,
                doctor_name: '',
                specialization: '',
                clinic_name: '',
                area: '',
                doctor_response: '',
                discussion_summary: '',
                competitor_medicines: '',
                products: [
                    { product_id: '', interest_level: '', remarks: '' }
                ],
                samples: [],
                orders: [],
                order_remarks: ''
            },

            init() {
                // Get Location immediately on load
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            this.form.lat = position.coords.latitude;
                            this.form.lng = position.coords.longitude;
                            this.form.accuracy = position.coords.accuracy;
                            this.locationStatus = 'GPS Captured Successfully';
                        },
                        (error) => {
                            this.locationStatus = 'Warning: Could not get GPS (' + error.message + ')';
                        }
                    );
                }
            },

            // Step Validators
            validateStep1() {
                this.errorMessage = '';
                if (!this.form.doctor_name.trim() || !this.form.specialization) {
                    this.errorMessage = "Doctor Name and Specialization are required.";
                    return;
                }
                this.step = 2;
            },

            validateStep2() {
                this.errorMessage = '';
                if (!this.form.doctor_response || !this.form.discussion_summary.trim()) {
                    this.errorMessage = "Doctor Response and Discussion Summary are required.";
                    return;
                }
                this.step = 3;
            },

            validateStep3() {
                this.errorMessage = '';
                let valid = true;
                
                // Remove empty rows if multiple
                this.form.products = this.form.products.filter(p => p.product_id !== '');
                
                if (this.form.products.length === 0) {
                    this.errorMessage = "At least one product must be discussed.";
                    this.addProductRow(); // give them a row back
                    return;
                }

                this.form.products.forEach(p => {
                    if (!p.product_id || !p.interest_level) {
                        valid = false;
                        this.errorMessage = "All discussed products must have a Product and Interest Level selected.";
                    }
                });

                if (valid) this.step = 4;
            },

            validateStep4() {
                this.errorMessage = '';
                let valid = true;
                
                this.form.samples = this.form.samples.filter(s => s.product_id !== '' && s.quantity > 0);

                this.form.samples.forEach(s => {
                    // Check against available quantity
                    let available = this.assignedSamples.find(as => as.product_id == s.product_id);
                    if (!available || s.quantity > available.remaining_quantity) {
                        valid = false;
                        this.errorMessage = `Cannot distribute ${s.quantity} of ${available.product_name}. Only ${available.remaining_quantity} available.`;
                    }
                });

                if (valid) this.step = 5;
            },

            validateStep5() {
                this.errorMessage = '';
                this.form.orders = this.form.orders.filter(o => o.product_id !== '' && o.quantity > 0);
                this.step = 6;
            },

            // Helpers
            addProductRow() { this.form.products.push({ product_id: '', interest_level: '', remarks: '' }); },
            removeProductRow(index) { this.form.products.splice(index, 1); },
            
            addSampleRow() { this.form.samples.push({ product_id: '', quantity: 1 }); },
            removeSampleRow(index) { this.form.samples.splice(index, 1); },
            
            addOrderRow() { this.form.orders.push({ product_id: '', quantity: 1 }); },
            removeOrderRow(index) { this.form.orders.splice(index, 1); },

            getProductName(id) {
                let p = this.allProducts.find(x => x.id == id);
                return p ? p.name : 'Unknown';
            },

            // Submit JSON Payload via Fetch
            async submitVisit() {
                this.errorMessage = '';
                const btn = document.getElementById('submit-btn');
                btn.innerHTML = 'Submitting...';
                btn.disabled = true;

                try {
                    const response = await fetch("{{ route('mr.visits.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Submission failed. Please check your data.');
                    }

                    if (data.success) {
                        window.location.href = data.redirect;
                    }
                } catch (error) {
                    this.errorMessage = error.message;
                    btn.innerHTML = 'Submit Visit';
                    btn.disabled = false;
                    window.scrollTo(0,0);
                }
            }
        }));
    });
</script>
@endsection
