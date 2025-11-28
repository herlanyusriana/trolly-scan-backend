@once
    <script>
        (() => {
            const initQrSelection = () => {
                window.qrSelection = ({ ids = [] } = {}) => {
                    const normalizeIds = (values) => {
                        return Array.from(
                            new Set(
                                values
                                    .map((value) => Number(value))
                                    .filter((value) => Number.isInteger(value) && value > 0),
                            ),
                        );
                    };

                    return {
                        ids: normalizeIds(ids),
                        selected: [],
                        allSelected: false,
                        get selectedSize() {
                            return this.selected.length;
                        },
                        isSelected(id) {
                            const normalized = Number(id);
                            return this.selected.includes(normalized);
                        },
                        toggle({ id }) {
                            const normalized = Number(id);
                            if (!this.ids.includes(normalized)) {
                                return;
                            }

                            if (this.isSelected(normalized)) {
                                this.selected = this.selected.filter((value) => value !== normalized);
                            } else {
                                this.selected = [...this.selected, normalized];
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
            };

            if (typeof window.qrSelection !== 'function') {
                initQrSelection();
            }

            document.addEventListener('alpine:init', initQrSelection);
        })();
    </script>
@endonce
