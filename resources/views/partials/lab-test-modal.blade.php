<!-- Lab Test Modal -->
<div id="lab-test-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[70] flex items-center justify-center hidden opacity-0 transition-opacity duration-300 p-4">
    <div class="bg-white border border-zinc-200 rounded-2xl w-full max-w-lg shadow-2xl transform scale-95 transition-transform duration-300 flex flex-col text-left max-h-[90vh] overflow-hidden">
        <div class="flex justify-between items-center border-b border-zinc-100 px-6 py-4 shrink-0">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-violet-50 text-violet-800 border border-violet-100 rounded-lg">
                    <i data-lucide="flask-conical" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-zinc-900" id="lab-test-modal-title">Add Lab Test Entry</h3>
                    <p class="text-[11px] text-zinc-500 mt-0.5">Entry <span id="lab-test-entry-id" class="font-mono font-semibold text-[#0d2818]">—</span></p>
                </div>
            </div>
            <button type="button" onclick="closeLabTestModal()" class="p-1.5 hover:bg-zinc-100 rounded-full text-zinc-400 hover:text-zinc-600 transition cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="lab-test-form" onsubmit="submitLabTest(event)" class="overflow-y-auto flex-1">
            <input type="hidden" name="entry_id" id="lab_test_entry_id" value="">

            <div id="lab-test-errors" class="hidden mx-6 mt-4 text-sm text-red-700 bg-red-50 border border-red-100 rounded-lg px-3 py-2 whitespace-pre-line"></div>

            <div class="px-6 py-4 space-y-4">
                <div class="rounded-lg bg-zinc-50 border border-zinc-100 px-3 py-2.5 text-[11px] text-zinc-500">
                    <span class="font-semibold text-zinc-700">Field readings:</span>
                    <span id="lab-test-field-readings" class="ml-1">—</span>
                </div>

                <div class="space-y-1">
                    <label for="lab_name" class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Lab / testing facility *</label>
                    <input type="text" name="lab_name" id="lab_name" required placeholder="e.g. Shiv Edibles Quality Lab, Kota"
                        class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                </div>

                <div class="space-y-1">
                    <label for="lab_test_status" class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Test result / status *</label>
                    <select name="lab_test_status" id="lab_test_status" required
                        class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition cursor-pointer">
                        <option value="pending">Pending</option>
                        <option value="pass">Pass</option>
                        <option value="fail">Fail</option>
                        <option value="retest">Retest required</option>
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label for="lab_moisture" class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Moisture % *</label>
                        <input type="number" name="lab_moisture" id="lab_moisture" required min="0" max="100" step="0.1"
                            class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                    </div>
                    <div class="space-y-1">
                        <label for="lab_fm" class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">F.M. % *</label>
                        <input type="number" name="lab_fm" id="lab_fm" required min="0" max="100" step="0.1"
                            class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                    </div>
                    <div class="space-y-1">
                        <label for="lab_dm" class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">D.M. % *</label>
                        <input type="number" name="lab_dm" id="lab_dm" required min="0" max="100" step="0.1"
                            class="w-full px-3 py-2 text-sm bg-zinc-50 border border-zinc-200 rounded-lg focus:border-[#0d2818] focus:bg-white focus:outline-none transition">
                    </div>
                </div>
            </div>

            <div class="border-t border-zinc-100 bg-zinc-50/80 px-6 py-4 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="closeLabTestModal()" class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-sm font-semibold rounded-lg transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit" id="lab-test-submit" class="bg-[#0d2818] hover:bg-[#163a23] text-white text-sm font-semibold py-2.5 px-5 rounded-lg transition duration-150 flex items-center gap-1.5 cursor-pointer shadow-sm shadow-[#0d2818]/10">
                    <i data-lucide="save" class="w-3.5 h-3.5"></i>
                    Save Lab Test
                </button>
            </div>
        </form>
    </div>
</div>
