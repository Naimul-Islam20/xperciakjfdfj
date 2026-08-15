document.addEventListener("DOMContentLoaded", () => {
    initMobileSidebar();
    initAdminSidebar();
    initAdminBulkSelect();
    initCollectionMega();
    initSearchOverlay();
    initHeroSlider();
    initProductDetail();
    initCollectionFilters();
    initScrollReveal();
    initContactForm();
    initAdminToasts();
    initSingleImageUploads();
});

function initMobileSidebar() {
    const root = document.querySelector("[data-mobile-sidebar]");
    const toggle = document.getElementById("mobile-menu-toggle");
    if (!root || !toggle) return;

    const closeButtons = root.querySelectorAll("[data-mobile-sidebar-close]");
    const accordion = root.querySelector("[data-mobile-accordion]");
    const accordionToggle = root.querySelector("[data-mobile-accordion-toggle]");
    const accordionPanel = root.querySelector(".mobile-sidebar-accordion-panel");
    let closeTimer = null;

    const setOpen = (open) => {
        window.clearTimeout(closeTimer);

        if (open) {
            root.hidden = false;
            // Force the closed position to render before starting the transition.
            root.getBoundingClientRect();
            window.requestAnimationFrame(() => root.classList.add("is-open"));
        } else {
            root.classList.remove("is-open");
            closeTimer = window.setTimeout(() => {
                if (!root.classList.contains("is-open")) root.hidden = true;
            }, 300);
        }

        toggle.setAttribute("aria-expanded", String(open));
        toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
        document.body.classList.toggle("mobile-sidebar-open", open);
    };

    toggle.addEventListener("click", () => {
        setOpen(!root.classList.contains("is-open"));
    });

    closeButtons.forEach((button) => {
        button.addEventListener("click", () => setOpen(false));
    });

    accordionToggle?.addEventListener("click", () => {
        const expanded = accordionToggle.getAttribute("aria-expanded") === "true";
        accordionToggle.setAttribute("aria-expanded", String(!expanded));
        accordionToggle.classList.toggle("is-open", !expanded);
        accordion?.classList.toggle("is-open", !expanded);

        if (accordionPanel) {
            accordionPanel.inert = expanded;
            accordionPanel.style.maxHeight = expanded
                ? "0px"
                : `${accordionPanel.scrollHeight}px`;
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && root.classList.contains("is-open")) {
            setOpen(false);
        }
    });

    if (accordionPanel) {
        accordionPanel.inert = true;
        accordionPanel.style.maxHeight = "0px";
    }
}

function initAdminSidebar() {
    const root = document.querySelector("[data-admin-sidebar]");
    const toggle = document.getElementById("admin-menu-toggle");
    if (!root || !toggle) return;

    const closeButtons = root.querySelectorAll("[data-admin-sidebar-close]");
    let closeTimer = null;

    const setOpen = (open) => {
        window.clearTimeout(closeTimer);

        if (open) {
            root.hidden = false;
            root.getBoundingClientRect();
            window.requestAnimationFrame(() => root.classList.add("is-open"));
        } else {
            root.classList.remove("is-open");
            closeTimer = window.setTimeout(() => {
                if (!root.classList.contains("is-open")) root.hidden = true;
            }, 300);
        }

        toggle.setAttribute("aria-expanded", String(open));
        toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
        document.body.classList.toggle("admin-sidebar-open", open);
    };

    toggle.addEventListener("click", () => {
        setOpen(!root.classList.contains("is-open"));
    });

    closeButtons.forEach((button) => {
        button.addEventListener("click", () => setOpen(false));
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && root.classList.contains("is-open")) {
            setOpen(false);
        }
    });

    window.addEventListener(
        "resize",
        () => {
            if (window.matchMedia("(min-width: 1024px)").matches && root.classList.contains("is-open")) {
                setOpen(false);
            }
        },
        { passive: true }
    );
}

function initAdminBulkSelect() {
    document.querySelectorAll("[data-admin-bulk-wrap]").forEach((wrap) => {
        const form = wrap.querySelector("[data-admin-bulk]");
        if (!form) return;

        const selectAll = form.querySelector("[data-admin-bulk-all]");
        const items = Array.from(form.querySelectorAll("[data-admin-bulk-item]"));
        const bar = form.querySelector("[data-admin-bulk-bar]");
        const countEl = form.querySelector("[data-admin-bulk-count]");
        const deleteBtn = form.querySelector(".admin-bulk-delete");
        const toggleBtn = wrap.querySelector("[data-admin-select-toggle]");
        const cancelBtn = wrap.querySelector("[data-admin-select-cancel]");

        if (!items.length) return;

        const setSelecting = (on) => {
            wrap.classList.toggle("is-selecting", on);
            form.classList.toggle("is-selecting", on);
            if (toggleBtn) toggleBtn.hidden = on;
            if (cancelBtn) cancelBtn.hidden = !on;

            if (!on) {
                items.forEach((item) => {
                    item.checked = false;
                });
                if (selectAll) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                }
            }

            sync();
        };

        const sync = () => {
            const selecting = wrap.classList.contains("is-selecting");
            const selected = items.filter((item) => item.checked);
            const count = selected.length;

            if (selectAll) {
                selectAll.checked = count > 0 && count === items.length;
                selectAll.indeterminate = count > 0 && count < items.length;
            }

            if (bar) bar.hidden = !selecting || count === 0;
            if (countEl) countEl.textContent = String(count);
        };

        toggleBtn?.addEventListener("click", () => setSelecting(true));
        cancelBtn?.addEventListener("click", () => setSelecting(false));

        selectAll?.addEventListener("change", () => {
            items.forEach((item) => {
                if (!item.disabled) item.checked = selectAll.checked;
            });
            sync();
        });

        items.forEach((item) => {
            item.addEventListener("change", sync);
        });

        form.addEventListener("submit", (event) => {
            if (!wrap.classList.contains("is-selecting")) {
                event.preventDefault();
                return;
            }

            const selected = items.filter((item) => item.checked);
            if (!selected.length) {
                event.preventDefault();
                return;
            }

            const message =
                deleteBtn?.getAttribute("data-admin-bulk-confirm") ||
                "Delete selected items?";
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });

        setSelecting(false);
    });
}

function initSingleImageUploads() {
    document.querySelectorAll("[data-single-image-upload]").forEach((root) => {
        const input = root.querySelector("[data-image-input]");
        const preview = root.querySelector("[data-image-preview]");
        const placeholder = root.querySelector("[data-image-placeholder]");
        if (!input || !preview) return;

        input.addEventListener("change", () => {
            const file = input.files?.[0];
            if (!file) return;

            const url = URL.createObjectURL(file);
            preview.src = url;
            preview.classList.remove("hidden");
            placeholder?.classList.add("hidden");

            preview.addEventListener(
                "load",
                () => URL.revokeObjectURL(url),
                { once: true },
            );
        });
    });
}

function initAdminToasts() {
    const stack = document.querySelector("[data-admin-toast-stack]");
    const source = document.querySelector("[data-admin-toasts]");
    if (!stack || !source) return;

    let messages = [];
    try {
        messages = JSON.parse(source.textContent || "[]");
    } catch {
        messages = [];
    }

    if (!Array.isArray(messages) || messages.length === 0) return;

    const dismiss = (toast) => {
        toast.classList.add("is-leaving");
        window.setTimeout(() => toast.remove(), 220);
    };

    messages.forEach((item, index) => {
        if (!item?.message) return;

        const toast = document.createElement("div");
        toast.className = `admin-toast admin-toast--${item.type || "success"}`;
        toast.setAttribute("role", "status");

        const text = document.createElement("p");
        text.className = "admin-toast-message";
        text.textContent = String(item.message);

        const close = document.createElement("button");
        close.type = "button";
        close.className = "admin-toast-close";
        close.setAttribute("aria-label", "Dismiss");
        close.innerHTML =
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/></svg>';
        close.addEventListener("click", () => dismiss(toast));

        toast.append(text, close);
        stack.appendChild(toast);

        window.requestAnimationFrame(() => toast.classList.add("is-visible"));
        window.setTimeout(() => dismiss(toast), 4200 + index * 350);
    });
}

function initContactForm() {
    const form = document.querySelector("[data-contact-form]");
    if (!form) return;

    const syncFilled = (field) => {
        const wrap = field.closest(".contact-field-wrap");
        if (!wrap) return;
        wrap.classList.toggle("is-filled", (field.value || "").trim() !== "");
    };

    form.querySelectorAll("input, textarea").forEach((field) => {
        syncFilled(field);
        field.addEventListener("input", () => syncFilled(field));
        field.addEventListener("blur", () => syncFilled(field));
    });
}

function initScrollReveal() {
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    const items = [];
    const seen = new Set();

    const addItem = (el, order = 0) => {
        if (!el || seen.has(el)) return;
        seen.add(el);
        el.classList.add("scroll-reveal");
        el.style.setProperty("--reveal-order", String(order));
        items.push(el);
    };

    // Home sections
    document
        .querySelectorAll(".collections-section, .best-sellers-section")
        .forEach((section) => {
            addItem(
                section.querySelector(
                    ".collections-heading, .best-sellers-heading",
                ),
                0,
            );
            section
                .querySelectorAll(".collection-card, .product-card")
                .forEach((el, index) => {
                    addItem(el, Math.min(index + 1, 6));
                });
            addItem(section.querySelector(".view-all-wrap"), 0);
        });

    // Collection / shop pages — skip toolbar/cards (transform breaks filter layering)
    document.querySelectorAll(".collection-page").forEach((page) => {
        addItem(page.querySelector(".collection-page-title"), 0);
        addItem(page.querySelector(".shop-pagination"), 0);
        addItem(page.querySelector("[data-collection-empty]"), 0);
    });

    // Product detail page
    document.querySelectorAll(".product-detail").forEach((section) => {
        addItem(section.querySelector(".product-gallery"), 0);
        addItem(section.querySelector(".product-info"), 1);
    });
    document.querySelectorAll(".related-products-section").forEach((section) => {
        addItem(section.querySelector(".related-products-heading"), 0);
        section.querySelectorAll(".product-card").forEach((el, index) => {
            addItem(el, Math.min(index + 1, 6));
        });
    });

    // Contact page
    document.querySelectorAll(".contact-page").forEach((page) => {
        addItem(page.querySelector(".contact-page-title"), 0);
        addItem(page.querySelector(".contact-success"), 1);
        addItem(page.querySelector(".contact-form"), 1);
        addItem(page.querySelector(".contact-details"), 2);
    });

    // Footer soft reveal
    document.querySelectorAll(".site-footer .footer-col").forEach((el, index) => {
        addItem(el, Math.min(index, 3));
    });
    addItem(document.querySelector(".site-footer .footer-social"), 0);
    addItem(document.querySelector(".site-footer .footer-bottom"), 0);

    // Markup-declared reveals, so nothing stays hidden if it is not listed above
    document.querySelectorAll(".scroll-reveal").forEach((el) => addItem(el, 0));

    if (!items.length) return;

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add("is-revealed");
                obs.unobserve(entry.target);
            });
        },
        {
            rootMargin: "0px 0px -50px 0px",
        },
    );

    items.forEach((el) => observer.observe(el));
}

function initCollectionFilters() {
    const form = document.querySelector("[data-collection-filters]");
    if (!form) return;

    const dropdowns = form.querySelectorAll("[data-filter-dropdown]");
    const grid = document.querySelector("[data-collection-grid]");
    const countEls = Array.from(
        document.querySelectorAll("[data-collection-count]"),
    );
    const countWraps = Array.from(
        document.querySelectorAll("[data-collection-count-wrap]"),
    );
    const countSpinners = Array.from(
        document.querySelectorAll("[data-collection-count-spinner]"),
    );
    const cards = Array.from(document.querySelectorAll("[data-product-card]"));
    const minInput = form.querySelector("[data-price-min]");
    const maxInput = form.querySelector("[data-price-max]");
    const availabilityOptions = Array.from(
        form.querySelectorAll("[data-availability-option]"),
    );
    const availabilitySelected = form.querySelector(
        "[data-availability-selected]",
    );
    const emptyFiltered = document.querySelector(
        "[data-collection-empty-filtered]",
    );
    const mobileQuery = window.matchMedia("(max-width: 767px)");
    const drawer = form.querySelector("[data-mobile-filter-drawer]");
    const drawerOpen = form.querySelector("[data-mobile-filter-open]");
    const drawerBackdrop = form.querySelector(
        ".collection-filter-drawer-backdrop",
    );
    const drawerClose = form.querySelectorAll("[data-mobile-filter-close]");
    let drawerCloseTimer = null;

    const setDrawerOpen = (open) => {
        if (!drawer || !drawerBackdrop) return;

        window.clearTimeout(drawerCloseTimer);

        if (open) {
            drawerBackdrop.hidden = false;
            dropdowns.forEach((dropdown) => {
                const menu = dropdown.querySelector("[data-filter-menu]");
                if (menu) menu.hidden = false;
            });
            drawerBackdrop.getBoundingClientRect();
            window.requestAnimationFrame(() => {
                drawer.classList.add("is-open");
                drawerBackdrop.classList.add("is-open");
            });
        } else {
            drawer.classList.remove("is-open");
            drawerBackdrop.classList.remove("is-open");
            drawerCloseTimer = window.setTimeout(() => {
                if (!drawer.classList.contains("is-open")) {
                    drawerBackdrop.hidden = true;
                }
            }, 300);
        }

        document.body.classList.toggle("collection-filter-drawer-open", open);
        drawerOpen?.setAttribute("aria-expanded", String(open));
        // Only lock the drawer on mobile; on desktop it is the visible toolbar.
        drawer.inert = mobileQuery.matches ? !open : false;
    };

    drawerOpen?.addEventListener("click", (event) => {
        event.preventDefault();
        event.stopPropagation();
        setDrawerOpen(true);
    });
    drawerClose.forEach((button) => {
        button.addEventListener("click", () => setDrawerOpen(false));
    });

    document.addEventListener("keydown", (event) => {
        if (
            event.key === "Escape" &&
            drawer?.classList.contains("is-open")
        ) {
            setDrawerOpen(false);
        }
    });

    const syncDrawerMode = () => {
        if (mobileQuery.matches) {
            if (drawer) drawer.inert = !drawer.classList.contains("is-open");
            dropdowns.forEach((dropdown) => {
                const menu = dropdown.querySelector("[data-filter-menu]");
                if (menu) menu.hidden = false;
            });
        } else {
            setDrawerOpen(false);
            if (drawer) drawer.inert = false;
            dropdowns.forEach((dropdown) => {
                dropdown.classList.remove("is-open");
                const menu = dropdown.querySelector("[data-filter-menu]");
                if (menu) menu.hidden = true;
            });
        }
    };

    mobileQuery.addEventListener("change", syncDrawerMode);
    syncDrawerMode();

    dropdowns.forEach((dropdown) => {
        const toggle = dropdown.querySelector("[data-filter-toggle]");
        const menu = dropdown.querySelector("[data-filter-menu]");
        if (!toggle || !menu) return;

        toggle.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (window.matchMedia("(max-width: 767px)").matches) return;

            const willOpen = !dropdown.classList.contains("is-open");

            dropdowns.forEach((item) => {
                item.classList.remove("is-open");
                item.querySelector("[data-filter-toggle]")?.setAttribute(
                    "aria-expanded",
                    "false",
                );
                const itemMenu = item.querySelector("[data-filter-menu]");
                if (itemMenu) itemMenu.hidden = true;
            });

            if (willOpen) {
                dropdown.classList.add("is-open");
                toggle.setAttribute("aria-expanded", "true");
                menu.hidden = false;
            }
        });
    });

    const updateCount = (visible) => {
        countEls.forEach((countEl) => {
            countEl.textContent = `${visible} ${visible === 1 ? "product" : "products"}`;
        });
    };

    const updateAvailabilitySelected = () => {
        if (!availabilitySelected) return;
        const selected = availabilityOptions.filter(
            (input) => input.checked,
        ).length;
        availabilitySelected.textContent = `${selected} selected`;
    };

    const setLoading = (loading) => {
        countWraps.forEach((countWrap) => {
            countWrap.classList.toggle("is-loading", loading);
        });
        grid?.classList.toggle("is-filtering", loading);
        countSpinners.forEach((countSpinner) => {
            countSpinner.hidden = !loading;
            countSpinner.style.display = loading ? "inline-block" : "";
        });
        countEls.forEach((countEl) => {
            countEl.hidden = loading;
        });
    };

    const syncUrl = () => {
        const params = new URLSearchParams();
        const sort = form.querySelector('input[name="sort"]:checked')?.value;
        if (sort && sort !== "featured") params.set("sort", sort);

        const minRaw = minInput?.value?.trim() ?? "";
        const maxRaw = maxInput?.value?.trim() ?? "";
        if (minRaw !== "") params.set("min_price", minRaw);
        if (maxRaw !== "") params.set("max_price", maxRaw);

        availabilityOptions.forEach((input) => {
            if (input.checked) params.append("availability[]", input.value);
        });

        const query = params.toString();
        const next = query ? `${form.action}?${query}` : form.action;
        window.history.replaceState({}, "", next);
    };

    const syncFilledState = (input) => {
        const wrap = input?.closest(".collection-price-input-wrap");
        if (!wrap) return;
        wrap.classList.toggle("is-filled", (input.value || "").trim() !== "");
    };

    const applyFilters = () => {
        const minRaw = minInput?.value?.trim() ?? "";
        const maxRaw = maxInput?.value?.trim() ?? "";
        const hasMin = minRaw !== "" && Number.isFinite(Number(minRaw));
        const hasMax = maxRaw !== "" && Number.isFinite(Number(maxRaw));
        const minVal = hasMin ? Number(minRaw) : 0;
        const maxVal = hasMax ? Number(maxRaw) : Number.POSITIVE_INFINITY;
        const selectedAvailability = availabilityOptions
            .filter((input) => input.checked)
            .map((input) => input.value);
        let visible = 0;

        cards.forEach((card) => {
            const price = Number(card.dataset.price || 0);
            const availability = card.dataset.availability || "in-stock";
            const priceOk = price >= minVal && price <= maxVal;
            const availabilityOk =
                selectedAvailability.length === 0 ||
                selectedAvailability.includes(availability);
            const show = priceOk && availabilityOk;
            card.hidden = !show;
            if (show) visible += 1;
        });

        updateCount(visible);
        updateAvailabilitySelected();
        if (emptyFiltered)
            emptyFiltered.hidden = visible !== 0 || cards.length === 0;
        syncUrl();
        setLoading(false);
    };

    let filterTimer = null;
    const scheduleFilters = () => {
        syncFilledState(minInput);
        syncFilledState(maxInput);
        updateAvailabilitySelected();
        setLoading(true);
        clearTimeout(filterTimer);
        filterTimer = setTimeout(applyFilters, 320);
    };

    minInput?.addEventListener("input", scheduleFilters);
    maxInput?.addEventListener("input", scheduleFilters);
    minInput?.addEventListener("change", scheduleFilters);
    maxInput?.addEventListener("change", scheduleFilters);

    availabilityOptions.forEach((input) => {
        input.addEventListener("change", scheduleFilters);
    });

    // Keep filter menus open while interacting
    form.querySelector("[data-price-filter]")?.addEventListener(
        "click",
        (e) => {
            e.stopPropagation();
        },
    );
    form.querySelector("[data-availability-filter]")?.addEventListener(
        "click",
        (e) => {
            e.stopPropagation();
        },
    );
    form.querySelector("[data-sort-filter]")?.addEventListener("click", (e) => {
        e.stopPropagation();
    });

    const sortLabel = form.querySelector("[data-sort-label]");
    const sortSelected = form.querySelector("[data-sort-selected]");
    const sortOptions = Array.from(form.querySelectorAll("[data-sort-option]"));

    const sortLabels = {
        featured: "Featured",
        "price-asc": "Price, low to high",
        "price-desc": "Price, high to low",
        "title-asc": "Alphabetically, A-Z",
        "title-desc": "Alphabetically, Z-A",
    };

    const applySortLabel = (value) => {
        const label = sortLabels[value] || "Featured";
        if (sortLabel) sortLabel.textContent = label;
        if (sortSelected) sortSelected.textContent = label;
    };

    sortOptions.forEach((input) => {
        input.addEventListener("change", () => {
            applySortLabel(input.value);
            setLoading(true);
            // Brief pause so spinner paints before navigation
            setTimeout(() => form.submit(), 120);
        });
    });

    document.addEventListener("click", (e) => {
        if (mobileQuery.matches) return;
        if (e.target.closest("[data-filter-dropdown]")) return;
        dropdowns.forEach((item) => {
            item.classList.remove("is-open");
            item.querySelector("[data-filter-toggle]")?.setAttribute(
                "aria-expanded",
                "false",
            );
            const itemMenu = item.querySelector("[data-filter-menu]");
            if (itemMenu) itemMenu.hidden = true;
        });
    });

    if (minInput) minInput.value = "";
    if (maxInput) maxInput.value = "";
    availabilityOptions.forEach((input) => {
        input.checked = false;
    });
    syncFilledState(minInput);
    syncFilledState(maxInput);
    updateAvailabilitySelected();
    applyFilters();
}

function initProductDetail() {
    const root = document.querySelector("[data-product-detail]");
    if (!root) return;

    const priceEl = root.querySelector("[data-product-price]");
    const packBtns = root.querySelectorAll("[data-product-pack]");
    const mainImage = root.querySelector("[data-product-main-image]");
    const thumbs = root.querySelectorAll("[data-product-thumb]");

    packBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            packBtns.forEach((item) => item.classList.remove("is-active"));
            btn.classList.add("is-active");
            if (priceEl && btn.dataset.priceLabel) {
                priceEl.textContent = btn.dataset.priceLabel;
            }
        });
    });

    thumbs.forEach((thumb) => {
        thumb.addEventListener("click", () => {
            const src = thumb.dataset.image;
            if (!src || !mainImage || mainImage.tagName !== "IMG") return;
            mainImage.src = src;
            thumbs.forEach((item) => item.classList.remove("is-active"));
            thumb.classList.add("is-active");
        });
    });

    const zoomBtn = root.querySelector("[data-product-zoom]");
    const lightbox = document.querySelector("[data-product-lightbox]");
    const lightboxImage = lightbox?.querySelector(
        "[data-product-lightbox-image]",
    );
    const lightboxPlaceholder = lightbox?.querySelector(
        "[data-product-lightbox-placeholder]",
    );
    const lightboxClose = lightbox?.querySelectorAll(
        "[data-product-lightbox-close]",
    );

    const openLightbox = () => {
        if (!lightbox) return;

        const hasImage =
            mainImage?.tagName === "IMG" && mainImage.getAttribute("src");

        if (hasImage && lightboxImage) {
            lightboxImage.hidden = false;
            lightboxImage.src = mainImage.src;
            lightboxImage.alt = mainImage.alt || "";
            if (lightboxPlaceholder) lightboxPlaceholder.hidden = true;
        } else {
            if (lightboxImage) {
                lightboxImage.hidden = true;
                lightboxImage.removeAttribute("src");
            }
            if (lightboxPlaceholder) lightboxPlaceholder.hidden = false;
        }

        lightbox.hidden = false;
        document.body.classList.add("product-lightbox-open");
        requestAnimationFrame(() => lightbox.classList.add("is-open"));
    };

    const closeLightbox = () => {
        if (!lightbox) return;
        lightbox.classList.remove("is-open");
        document.body.classList.remove("product-lightbox-open");
        const onEnd = () => {
            lightbox.hidden = true;
            lightbox.removeEventListener("transitionend", onEnd);
        };
        lightbox.addEventListener("transitionend", onEnd);
        setTimeout(() => {
            if (!lightbox.classList.contains("is-open")) lightbox.hidden = true;
        }, 280);
    };

    zoomBtn?.addEventListener("click", (e) => {
        e.preventDefault();
        openLightbox();
    });

    const galleryMain = root.querySelector(".product-gallery-main");
    galleryMain?.addEventListener("click", (e) => {
        if (e.target.closest("[data-product-zoom]")) return;
        openLightbox();
    });
    galleryMain?.style.setProperty("cursor", "zoom-in");

    const accordion = root.querySelector("[data-product-accordion]");
    const accordionTrigger = accordion?.querySelector(
        "[data-accordion-trigger]",
    );
    const accordionPanel = accordion?.querySelector("[data-accordion-panel]");

    accordionTrigger?.addEventListener("click", () => {
        const isOpen = accordion.classList.toggle("is-open");
        accordionTrigger.setAttribute("aria-expanded", String(isOpen));
        accordionPanel.hidden = !isOpen;
    });

    lightboxClose?.forEach((el) => {
        el.addEventListener("click", (e) => {
            e.preventDefault();
            closeLightbox();
        });
    });

    lightbox?.addEventListener("click", (e) => {
        if (e.target === lightbox) closeLightbox();
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && lightbox?.classList.contains("is-open")) {
            closeLightbox();
        }
    });
}
function initSearchOverlay() {
    const overlay = document.querySelector("[data-search-overlay]");
    const openBtns = document.querySelectorAll("[data-search-open]");
    const closeEls = document.querySelectorAll("[data-search-close]");
    const input = document.querySelector("[data-search-input]");

    if (!overlay) return;

    const preventScroll = (e) => {
        e.preventDefault();
    };

    const open = () => {
        overlay.hidden = false;
        // Force reflow so slide-in animation runs
        void overlay.offsetHeight;
        overlay.classList.add("is-open");
        input?.focus({ preventScroll: true });
        overlay.addEventListener("wheel", preventScroll, { passive: false });
        overlay.addEventListener("touchmove", preventScroll, {
            passive: false,
        });
    };

    const close = () => {
        overlay.classList.remove("is-open");
        overlay.removeEventListener("wheel", preventScroll);
        overlay.removeEventListener("touchmove", preventScroll);
        input?.blur();

        const onEnd = (e) => {
            if (e.target !== overlay.querySelector(".search-overlay-panel"))
                return;
            overlay.hidden = true;
            overlay.removeEventListener("transitionend", onEnd);
        };
        overlay.addEventListener("transitionend", onEnd);
        setTimeout(() => {
            if (!overlay.classList.contains("is-open")) overlay.hidden = true;
        }, 320);
    };

    openBtns.forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            open();
        });
    });

    closeEls.forEach((el) => {
        el.addEventListener("click", (e) => {
            e.preventDefault();
            close();
        });
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && overlay.classList.contains("is-open"))
            close();
    });
}

function initCollectionMega() {
    const nav = document.querySelector("[data-collection-nav]");
    const mega = document.querySelector("[data-collection-mega]");
    const toggle = document.querySelector("[data-collection-toggle]");

    if (!nav || !mega || !toggle) return;

    const open = () => {
        mega.hidden = false;
        nav.classList.add("is-open");
        toggle.setAttribute("aria-expanded", "true");
    };

    const close = () => {
        mega.hidden = true;
        nav.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
    };

    // Always start closed (also fixes browser back / bfcache restoring open state)
    close();
    window.addEventListener("pageshow", () => close());

    toggle.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (mega.hidden) open();
        else close();
    });

    mega.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", () => close());
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") close();
    });

    document.addEventListener("click", (e) => {
        if (!nav.contains(e.target) && !mega.contains(e.target)) close();
    });
}

function initHeroSlider() {
    const root = document.querySelector("[data-hero-slider]");
    if (!root) return;

    const slides = Array.from(document.querySelectorAll("[data-slide]"));
    const dots = Array.from(document.querySelectorAll("[data-hero-dot]"));
    const prevBtn = document.querySelector("[data-hero-prev]");
    const nextBtn = document.querySelector("[data-hero-next]");
    const playBtn = document.querySelector("[data-hero-play]");
    const playIcon = playBtn?.querySelector(".play-icon");
    const pauseIcon = playBtn?.querySelector(".pause-icon");

    let index = 0;
    let playing = true;
    let timer = null;

    const show = (next) => {
        index = (next + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            slide.classList.toggle("is-active", i === index);
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle("is-active", i === index);
        });

        const active = slides[index];
        const cta = root.querySelector("[data-hero-cta]");
        if (cta && active) {
            const text = active.dataset.buttonText;
            const link = active.dataset.buttonLink;
            if (text) cta.textContent = text;
            if (link) cta.setAttribute("href", link);
        }
    };

    const stop = () => {
        playing = false;
        if (timer) clearInterval(timer);
        timer = null;
        playBtn?.setAttribute("aria-label", "Play slideshow");
        playBtn?.setAttribute("aria-pressed", "true");
        playIcon?.classList.remove("hidden");
        pauseIcon?.classList.add("hidden");
    };

    const start = () => {
        playing = true;
        if (timer) clearInterval(timer);
        timer = setInterval(() => show(index + 1), 5000);
        playBtn?.setAttribute("aria-label", "Pause slideshow");
        playBtn?.setAttribute("aria-pressed", "false");
        playIcon?.classList.add("hidden");
        pauseIcon?.classList.remove("hidden");
    };

    prevBtn?.addEventListener("click", () => {
        show(index - 1);
        if (playing) start();
    });

    nextBtn?.addEventListener("click", () => {
        show(index + 1);
        if (playing) start();
    });

    dots.forEach((dot) => {
        dot.addEventListener("click", () => {
            show(Number(dot.dataset.heroDot));
            if (playing) start();
        });
    });

    playBtn?.addEventListener("click", () => {
        if (playing) stop();
        else start();
    });

    show(0);
    start();
}
