@once
    @push('scripts')
        <script type="module">
            window.qrSelection = window.qrSelection ?? function ({ ids = [] } = {}) {
                return {
                    ids: Array.from(new Set(ids)),
                    selected: [],
                    allSelected: false,
                    get selectedSize() {
                        return this.selected.length;
                    },
                    isSelected(id) {
                        return this.selected.includes(id);
                    },
                    toggle({ id }) {
                        if (!this.ids.includes(id)) {
                            return;
                        }

                        if (this.isSelected(id)) {
                            this.selected = this.selected.filter((value) => value !== id);
                        } else {
                            this.selected = [...this.selected, id];
                        }

                        this.allSelected = this.selected.length === this.ids.length && this.ids.length > 0;
                    },
                    toggleSelectAll() {
                        const checkboxes = this.$root.querySelectorAll('input[data-qr-checkbox]');

                        if (this.allSelected) {
                            this.selected = [];
                            this.allSelected = false;
                            checkboxes.forEach((checkbox) => {
                                checkbox.checked = false;
                            });
                            return;
                        }

                        this.selected = [...this.ids];
                        this.allSelected = this.ids.length > 0;
                        checkboxes.forEach((checkbox) => {
                            const id = parseInt(checkbox.value, 10);
                            checkbox.checked = this.isSelected(id);
                        });
                    },
                    get printHref() {
                        if (this.selected.length === 0) {
                            return '#';
                        }

                        const params = new URLSearchParams();
                        params.set('ids', this.selected.join(','));

                        return '{{ route('trolleys.print') }}' + '?' + params.toString();
                    },
                };
            };
        </script>
    @endpush
@endonce
