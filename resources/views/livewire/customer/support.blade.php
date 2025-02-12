<div class="space-y-6">
  
    <x-card title="Contact Support" separator>
        <form wire:submit.prevent="submitInquiry" class="space-y-4">
           
            <div>
                <label for="subject" class="block font-medium text-sm">Sujet</label>
                <x-input id="subject" wire:model="subject" placeholder="Sujet de votre demande" class="w-full" />
                @error('subject')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

           
            <div>
                <label for="message" class="block font-medium text-sm">Message</label>
                <x-textarea id="message" wire:model="message" placeholder="Décrivez votre problème ou demande" class="w-full" />
                @error('message')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <x-button type="submit" label="Envoyer" icon="o-paper-airplane" spinner="submitInquiry" class="btn-primary" />
            </div>
        </form>
    </x-card>

    <!-- List of Customer's Inquiries -->
    <x-card title="Vos demandes" separator>
        @if($inquiries->count())
            <div class="divide-y">
                @foreach($inquiries as $inquiry)
                    <div class="py-2">
                        <div class="flex justify-between">
                            <div class="font-semibold">
                                {{ $inquiry->subject ?? 'Sans sujet' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $inquiry->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div class="text-sm">{{ $inquiry->message }}</div>
                        <div class="mt-1">
                            <x-badge :value="$inquiry->status" class="{{ $inquiry->status === 'open' ? 'badge-error' : 'badge-success' }}" />
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div>Aucune demande trouvée.</div>
        @endif
    </x-card>
</div>
