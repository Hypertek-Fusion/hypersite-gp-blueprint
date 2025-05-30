"use strict";

function tabs_script() {
	class Tabs {
		constructor(element) {
			this.element = element;
			this.options = JSON.parse(
				this.element.getAttribute("data-fr-tabs-options") || "{}"
			);
			this.tabLinks = this.element.querySelectorAll(".fr-tabs__link");
			this.contentWrapper = this.options.contentOutside
				? document.querySelector(this.options.contentOutsideSelector)
				: this.element.querySelector(".fr-tabs__content-wrapper");
			this.contentItems = this.contentWrapper.querySelectorAll(
				".fr-tabs__content-item-wrapper"
			);
			this.tabList = this.element.querySelector(".fr-tabs__list");
			this.init();
			this.accordionClickListeners = new Map();
			this.resizeListenerAdded = false;
			this.accordionListenerAdded = false;
			this.clonedContentItems = [];

			if (this.options.isChangeToAccordion) {
				this.initAccordion(this.options.accordionOnDevice);
			}

			//const orientationVertical = this.options.isHorizontal === 0;
			if (this.options.isHorizontal !== 0) {
				this.tabList.setAttribute("aria-orientation", "horizontal");
			} else {
				this.tabList.setAttribute("aria-orientation", "vertical");
			}
		}

		init() {
			this.toggleDirectionClass();
			this.tabLinks.forEach((tabLink, index) => {
				tabLink.addEventListener("click", (event) => {
					event.preventDefault();
					this.switchTab(tabLink, index);
				});
				this.addKeyboardNavigation(tabLink, index);
			});

			// TODO @nuno, remove this in next release once confirmed that no problems have been caused, the same as the other code for arrows
			// this.contentItems.forEach((contentItem, tabIndex) => {
			// 	this.addContentItemKeyboardNavigation(contentItem, tabIndex);
			// });

			if (this.options.animate && this.options.animation.target) {
				this.createAnimatedElement();
				window.addEventListener("resize", () => {
					this.updateBackgroundAnimation();
				});
			}

			if (this.options.activeTab > this.tabLinks.length - 1) {
				console.warn(
					"The activeTab option is higher or lower than the number of tabs. The first tab will be selected instead."
				);
			}
			if (
				!this.options.activeTab ||
				this.options.activeTab > this.tabLinks.length - 1 ||
				this.options.activeTab < 0
			) {
				this.options.activeTab = 0;
			}

			this.switchTab(
				this.tabLinks[this.options.activeTab],
				Number(this.options.activeTab)
			);
			this.handleScrollToHash();
			this.addRoles();
			this.addAriaControls();

			this.clickToCenterTabLink();

			// TODO removing it for now, lets see if there is an isolated case about it
			/*this.hideHorizontalScrollbar();
			window.addEventListener("resize", () => {
				this.hideHorizontalScrollbar();
			});*/

			this.setListDirection();
		}

		isBuilder() {
			return document.querySelector(".iframe.mounted") ? true : false;
		}
		/////////////////////////////
		// Start accordion methods //
		/////////////////////////////

		initAccordion(breakpoint) {
			if (!this.resizeListenerAdded) {
				const handleResize = () => {
					if (window.innerWidth <= breakpoint) {
						if (!this.accordionListenerAdded) {
							this.switchToAccordion();
							this.addAccordionClickListeners();
							this.accordionListenerAdded = true;
						}
					} else {
						if (this.accordionListenerAdded) {
							this.removeAccordionClickListeners();
							this.switchToTabs();
							this.accordionListenerAdded = false;
						}
					}
				};

				// Add listeners on initial load
				handleResize();

				// Update listeners on resize
				window.addEventListener("resize", handleResize);

				this.resizeListenerAdded = true;
			}
		}

		toggleAccordionClasses(state) {
			if (state === "tabs") {
				this.element.classList.remove("fr-tabs--accordion");
				this.element.classList.add("fr-tabs--tabs");
				this.tabList.classList.remove("fr-tabs__list--accordion");
				this.tabList.classList.add("fr-tabs__list--tabs");
			} else if (state === "accordion") {
				this.element.classList.add("fr-tabs--accordion");
				this.element.classList.remove("fr-tabs--tabs");
				this.tabList.classList.add("fr-tabs__list--accordion");
				this.tabList.classList.remove("fr-tabs__list--tabs");
			}
		}

		//todo starts here
		switchToAccordion() {
			this.removeVisibleContentItems();
			this.moveContentItemsToAccordion();
			this.toggleAccordionClasses("accordion");
			this.previewAllContentItemsInBuilder();
		}

		moveContentItemsToAccordion() {
			const activeTab = this.element.querySelector(".fr-tabs__link.active");
			if (activeTab) {
				const index = Array.from(this.tabLinks).indexOf(activeTab);
				const contentItem = this.contentItems[index];

				this.addContentItem(activeTab, contentItem);

			} else if (this.tabLinks.length > 0) {
				const firstTab = this.tabLinks[0];
				const firstContentItem = this.contentItems[0];

				this.addContentItem(firstTab, firstContentItem);

			}
		}

		switchToTabs() {
			this.moveContentItemsToTabs();
			this.toggleAccordionClasses("tabs");
			this.endPreviewAllContentItemsInBuilder();
		}

		moveContentItemsToTabs() {
			this.contentItems.forEach((contentItem) => {
				// remove all content items first, to ensure correct order
				contentItem.parentNode?.removeChild(contentItem);
			});

			this.contentItems.forEach((contentItem) => {
				// Move the element
				this.contentWrapper.appendChild(contentItem);
			});
		}

		previewAllContentItemsInBuilder() {
			if (this.isBuilder()) {
				// Clear any previous cloned items
				this.clonedContentItems.forEach((clone) => {
					if (clone && clone.parentNode) {
						clone.parentNode.removeChild(clone);
					}
				});
				this.clonedContentItems = [];

				// Clone and append each contentItem after its corresponding tabLink
				this.tabLinks.forEach((tabLink, index) => {
					if (this.contentItems[index]) {
						const clone = this.contentItems[index].cloneNode(true); // Deep clone
						clone.classList.add("fr-builder-preview-content"); // Add unique class to the cloned conten
						tabLink.insertAdjacentElement("afterend", clone);
						this.clonedContentItems.push(clone);
					}
				});
			}
		}

		endPreviewAllContentItemsInBuilder() {
			if (this.isBuilder()) {
				this.clonedContentItems.forEach((clone) => {
					// Remove the cloned contentItem
					if (clone && clone.parentNode) {
						clone.parentNode.removeChild(clone);
					}
				});
				this.clonedContentItems = []; // Reset the array
			}
		}

		accordionClickListener(tabLink, index) {
			return () => {
				const contentItem = this.contentItems[index];
				const closePreviousAccordion = true;

				if (closePreviousAccordion) {
					this.removeVisibleContentItems();
					if (this.isNewContentItem(tabLink)) {
						this.addContentItemWithTransition(tabLink, contentItem);
					} else {
						this.removeContentItemWithTransition(tabLink);
					}
				} else {
					if (this.isBuilder()) return;
					if (this.isNewContentItem(tabLink)) {
						this.addContentItemWithTransition(tabLink, contentItem);
					} else {
						this.removeContentItemWithTransition(tabLink);
					}
				}
			};
		}

		addContentItemWithTransition(tabLink, contentItem) {
			tabLink.parentNode.insertBefore(contentItem, tabLink.nextElementSibling);

			// Force a reflow to get the computed styles
			window.getComputedStyle(contentItem).height;
			const contentHeight = contentItem.offsetHeight;

			contentItem.style.height = 0;
			const animationDuration = this.options.accordionDuration;
			contentItem.style.transition = `height ${animationDuration}ms ease-out`;
			setTimeout(() => {
				contentItem.style.height = contentHeight + "px";
			}, 0);
		}

		removeContentItemWithTransition(tabLink) {
			//todo
			if (this.isBuilder()) {
				console.log(
					"In builder mode, skipping removal of cloned content item."
				);
				return;
			}

			let content = tabLink.nextElementSibling;

			if (!content.classList.contains("fr-tabs__content-item-wrapper")) {
				console.error("Next sibling is not a content item");
				return;
			}

			const animationDuration = this.options.accordionDuration;
			// content.style.transition = "height 0.3s ease-in";
			content.style.transition = `height ${animationDuration}ms ease-in`;
			setTimeout(() => {
				content.style.height = 0;
				setTimeout(() => {
					if (content.parentNode) {
						content.parentNode.removeChild(content);
						content.style.height = null;
					}
				}, 300);
			}, 0);
		}

		removeVisibleContentItems() {
			const visibleItems = this.tabList.querySelectorAll(
				".fr-tabs__content-item-wrapper"
			);

			visibleItems.forEach((contentItem) => {
				const prevElement = contentItem.previousElementSibling;
				if (prevElement && prevElement.classList.contains("fr-tabs__link")) {
					this.removeContentItemWithTransition(prevElement);
				} else {
					contentItem.remove(); // This should not happen in normal case, but just to be sure not to leave any orphan contentItems
				}
			});
		}

		isNewContentItem(tabLink) {
			return (
				tabLink.nextSibling === null ||
				!tabLink.nextSibling.classList.contains("fr-tabs__content-item-wrapper")
			);
		}

		addContentItem(tabLink, contentItem) {
			tabLink.parentNode.insertBefore(contentItem, tabLink.nextSibling);
		}

		removeContentItem(tabLink) {
			tabLink.parentNode.removeChild(tabLink.nextSibling);
		}

		addAccordionClickListeners() {
			this.tabLinks.forEach((tabLink, index) => {
				const listener = this.accordionClickListener(tabLink, index);
				this.accordionClickListeners.set(tabLink, listener); // save the listener in the map
				tabLink.addEventListener("click", listener);

				// Adding a keydown event listener for the "Enter" key
				tabLink.addEventListener("keydown", (event) => {
					if (event.key === "Enter" || event.keyCode === 13) {
						// "Enter" key
						event.preventDefault();
						listener(); // Trigger the accordion toggle logic
					}
				});
			});
		}

		removeAccordionClickListeners() {
			this.tabLinks.forEach((tabLink) => {
				const listener = this.accordionClickListeners.get(tabLink); // get the listener from the map
				if (listener) {
					tabLink.removeEventListener("click", listener);

					// Remove the keydown event listener for the "Enter" key
					tabLink.removeEventListener("keydown", (event) => {
						if (event.key === "Enter" || event.keyCode === 13) {
							// "Enter" key
							event.preventDefault();
							listener(); // Trigger the accordion toggle logic
						}
					});
				}
			});
			this.accordionClickListeners.clear(); // clear the map
		}

		///////////////////////////
		// End accordion methods //
		///////////////////////////

		///////////////////////
		// METHODS
		///////////////////////

		setListDirection() {
			if (this.options.isHorizontal) {
				this.tabList.classList.add("horizontal");
				this.tabList.classList.remove("vertical");
			} else {
				this.tabList.classList.add("vertical");
				this.tabList.classList.remove("horizontal");
			}
		}

		toggleDirectionClass() {
			if (this.options.isHorizontal) {
				this.tabList.classList.add("fr-tabs__list--horizontal");
				this.tabList.classList.remove("fr-tabs__list--vertical");
			} else {
				this.tabList.classList.add("fr-tabs__list--vertical");
				this.tabList.classList.remove("fr-tabs__list--horizontal");
			}
		}

		clickToCenterTabLink() {
			const tabLinksWidth = this.tabList.scrollWidth;
			const tabsWrapperWidth = this.tabList.offsetWidth;
			if (tabLinksWidth > tabsWrapperWidth) {
				this.tabLinks.forEach((tabLink) => {
					tabLink.addEventListener("click", () => {
						const tabLinkPosition = tabLink.offsetLeft;
						const tabLinkWidth = tabLink.offsetWidth;
						const tabLinkCenter = tabLinkPosition + tabLinkWidth / 2;
						const scrollAmount = tabLinkCenter - tabsWrapperWidth / 2;
						this.tabList.scrollTo({
							left: scrollAmount,
							behavior: "smooth",
						});
					});
				});
			}
		}

		// Horizontal scrollbar visibility

		// TODO removing it for now, lets see if there is an isolated case about it
		/*hideHorizontalScrollbar() {
			const tabLinksWidth = this.tabList.scrollWidth;
			const tabsWrapperWidth = this.tabList.offsetWidth;
			if (tabLinksWidth <= tabsWrapperWidth) {
				this.tabList.classList.add("fr-tabs__list--no-scroll");
			} else {
				this.tabList.classList.remove("fr-tabs__list--no-scroll");
			}
		}*/

		// Scroll To hash on load or on click

		handleScrollToHash() {
			if (!this.options.scrollToHash) return;
			const hash = window.location.hash;

			if (hash) {
				const tabLink = this.element.querySelector(
					`.fr-tabs__link[href="${hash}"]`
				);
				if (tabLink) {
					const tabIndex = Array.from(this.tabLinks).indexOf(tabLink);
					this.scrollToTabAndActivate(tabLink, tabIndex);
				}
			} else {
				this.tabLinks.forEach((tabLink, index) => {
					const id = tabLink.getAttribute("href");
					if (id) {
						tabLink.addEventListener("click", (e) => {
							e.preventDefault();
							this.scrollToTabAndActivate(tabLink, index);
						});
					}
				});
			}
		}

		// TODO : duration time for scrolling doesn't work
		scrollToTabAndActivate(tabLink, index) {
			this.switchTab(tabLink, index);
			const offsetPosition =
				tabLink.offsetTop - this.options.scrollToHashOffset;
			scrollTo(offsetPosition, 500, easeOutCuaic);
		}

		// helper function to define if navigation is horizontal or vertical

		// Keyboard navigation in nav and inside content

		addKeyboardNavigation(tabLink, index) {
			tabLink.addEventListener("keydown", (event) => {
				let newIndex;
				switch (event.key) {
					// TODO: Nuno, remove code on a next release if indeed is verified that the behaviour did not cause any troubles on previews Tabs from users
					/*case "Enter":
                const contentItem = this.contentItems[index];
                const firstFocusableElement = contentItem.querySelector("a[href], button, input, select, textarea, [tabindex]:not([tabindex='-1'])");
                if (firstFocusableElement) {
                    event.preventDefault();
                    firstFocusableElement.focus();
                } else {
                    // Move to the next tab link
                    event.preventDefault();
                    newIndex = (index < this.tabLinks.length - 1) ? index + 1 : 0;
                    this.switchTab(this.tabLinks[newIndex], newIndex);
                    this.tabLinks[newIndex].focus();
                }
                break;*/
					case "ArrowLeft": // Applies to horizontal tabs
						if (this.options.isHorizontal) {
							event.preventDefault();
							newIndex = index > 0 ? index - 1 : this.tabLinks.length - 1;
							this.switchTab(this.tabLinks[newIndex], newIndex);
							this.tabLinks[newIndex].focus();
						}
						break;
					case "ArrowRight": // Applies to horizontal tabs
						if (this.options.isHorizontal) {
							event.preventDefault();
							newIndex = index < this.tabLinks.length - 1 ? index + 1 : 0;
							this.switchTab(this.tabLinks[newIndex], newIndex);
							this.tabLinks[newIndex].focus();
							// } else { // TODO: Nuno, remove the code in a next release once we confirm that there is no problems
							// 	const contentItem = this.contentItems[index];
							// 	const clickableElement = contentItem.querySelector("a, button, input, select, textarea");
							// 	if (clickableElement) {
							// 		event.preventDefault();
							// 		clickableElement.focus();
							// 	}
						}
						break;
					case "ArrowDown": // applies to the Vertical tabs
						if (!this.options.isHorizontal) {
							event.preventDefault();
							newIndex = index < this.tabLinks.length - 1 ? index + 1 : 0;
							this.switchTab(this.tabLinks[newIndex], newIndex);
							this.tabLinks[newIndex].focus();
						} /*else {// TODO: Nuno, remove code on a next release if indeed is verified that the behaviour did not cause any troubles on previews Tabs from users
							const contentItem = this.contentItems[index];
							const clickableElement = contentItem.querySelector("a, button, input, select, textarea");
							if (clickableElement) {
								event.preventDefault();
								clickableElement.focus();
							}
						}*/
						break;
					case "ArrowUp": // Applies to the Vertical tabs
						if (!this.options.isHorizontal) {
							event.preventDefault();
							newIndex = index > 0 ? index - 1 : this.tabLinks.length - 1;
							this.switchTab(this.tabLinks[newIndex], newIndex);
							this.tabLinks[newIndex].focus();
						}
						break;
				}
			});
		}

		//TODO: Nuno, this full block of code is not needed since that there should not be any event to navigate with the arrows, we can remove this code in a next release once confirmed that this are not causing a problem
		// Inside of a tab Content, the navigation should be only with the Tab Key,
		// addContentItemKeyboardNavigation(contentItem, tabIndex) {
		// 	const focusableElements = contentItem.querySelectorAll("a, button, input, select, textarea ");
		// 	focusableElements.forEach((focusableElement, index) => {
		// 		focusableElement.addEventListener("keydown", (event) => {
		// 			switch (event.key) {
		// 				case "ArrowUp":
		// 					if (!this.options.isHorizontal) {
		// 						event.preventDefault();
		// 						const previousFocusableElement = focusableElements[index - 1];
		// 						if (previousFocusableElement) {
		// 							previousFocusableElement.focus();
		// 						} else if (this.tabLinks[tabIndex - 1]) {
		// 							this.tabLinks[tabIndex - 1].focus();
		// 						} else {
		// 							this.tabLinks[tabIndex].focus();
		// 						}
		// 					} else {
		// 						event.preventDefault();
		// 						this.tabLinks[tabIndex].focus();
		// 					}
		// 					break;
		// 				case "ArrowRight":
		// 					if (this.options.isHorizontal) {
		// 						event.preventDefault();
		// 						const nextFocusableElement = focusableElements[index + 1];
		// 						if (nextFocusableElement) {
		// 							nextFocusableElement.focus();
		// 						} else if (this.tabLinks[tabIndex + 1]) {
		// 							this.tabLinks[tabIndex + 1].focus();
		// 						} else {
		// 							this.tabLinks[tabIndex].focus();
		// 						}
		// 					}
		// 					break;
		// 				case "ArrowLeft":
		// 					if (this.options.isHorizontal) {
		// 						event.preventDefault();
		// 						const previousFocusableElement = focusableElements[index - 1];
		// 						if (previousFocusableElement) {
		// 							previousFocusableElement.focus();
		// 						} else if (this.tabLinks[tabIndex - 1]) {
		// 							this.tabLinks[tabIndex - 1].focus();
		// 						} else {
		// 							this.tabLinks[tabIndex].focus();
		// 						}
		// 					} else {
		// 						// like using arrow up for horizontal
		// 						event.preventDefault();
		// 						this.tabLinks[tabIndex].focus();
		// 					}
		// 					break;
		// 				case "ArrowDown":
		// 					if (!this.options.isHorizontal) {
		// 						event.preventDefault();
		// 						const nextFocusableElement = focusableElements[index + 1];
		// 						if (nextFocusableElement) {
		// 							nextFocusableElement.focus();
		// 						} else if (this.tabLinks[tabIndex + 1]) {
		// 							this.tabLinks[tabIndex + 1].focus();
		// 						} else {
		// 							this.tabLinks[tabIndex].focus();
		// 						}
		// 					}
		// 					break;
		// 			}
		// 		});
		// 	});
		// }

		// Animation methods

		//TODO Animated
		createAnimatedElement() {
			const activeTab = this.options.activeTab;

			this.animatedElement = document.createElement("div");
			this.animatedElement.className = "fr-tabs__animation";
			this.animatedElement.setAttribute("aria-hidden", "true");
			this.tabList.appendChild(this.animatedElement);
			/*if (activeTab !== 0) {
				this.animatedElement.style.position
			}*/
			if (this.tabLinks && activeTab < this.tabLinks.length) {
				this.positionAnimatedElement(this.tabLinks[activeTab]);
			}
		}

		positionAnimatedElement(activeTabElement) {
			setTimeout(() => {
				const tabWidth = activeTabElement.offsetWidth;
				const tabLeft = activeTabElement.offsetLeft;
				const tabHeight = activeTabElement.offsetHeight;
				const tabTop = activeTabElement.offsetTop;

				this.animatedElement.style.width = `${tabWidth}px`;
				this.animatedElement.style.left = `${tabLeft}px`;
				this.animatedElement.style.height = `${tabHeight}px`;
				this.animatedElement.style.top = `${tabTop}px`;

				const duration = this.options.animation.duration || 300;
				this.animatedElement.style.transition = `all ${duration}ms ease-in-out`;
			}, 0);
		}

		handleActiveLinkAnimation(newActiveTab) {
			const isAccordion = this.tabList.classList.contains("fr-tabs__list--accordion");
			if (!this.options.isChangeToAccordion) {
				this.tabLinks.forEach((tabLink) => {
					tabLink.classList.remove("fr-tabs__link--trans");
				});
				newActiveTab.classList.add("fr-tabs__link--trans");
			}

			this.positionAnimatedElement(newActiveTab);

			const duration = isAccordion ? 0 : this.options.animation.duration;
			if (this.options.animation.duration) {
				this.tabLinks.forEach((tabLink) => {
					// tabLink.style.transition = `all ${this.options.animation.duration}ms ease-in-out`;
					tabLink.style.transition = `all ${duration}ms ease-in-out`;
				});
			}
		}

		updateBackgroundAnimation() {
			// Only proceed if there's a background animation
			if (!this.animatedElement) {
				return;
			}

			const activeTab = this.element.querySelector(".fr-tabs__link.active");
			if (activeTab) {
				this.handleActiveLinkAnimation(activeTab);
			}
		}

		// Accessibility

		addRoles() {
			this.tabList.setAttribute("role", "tablist");
			this.tabLinks.forEach((tabLink) => {
				tabLink.setAttribute("role", "tab");
			});
			this.contentItems.forEach((contentItem) => {
				contentItem.setAttribute("role", "tabpanel");
			});
		}

		addAriaControls() {
			this.tabLinks.forEach((tabLink, index) => {
				if (tabLink.id) return;
				tabLink.id = `fr-tabs__link-${index}`;
			});
			this.contentItems.forEach((contentItem, index) => {
				if (contentItem.id) return;
				contentItem.id = `fr-tabs__panel-${index}`;
			});

			this.tabLinks.forEach((tabLink, index) => {
				const tabPanelId = this.contentItems[index].id;
				tabLink.setAttribute("aria-controls", tabPanelId);
			});
			this.contentItems.forEach((contentItem, index) => {
				const tabPanelId = this.tabLinks[index].id;
				contentItem.setAttribute("aria-labelledby", tabPanelId);
			});
		}

		// Main Tabs Switch Method

		switchTab(newActiveTab, index) {
			if (newActiveTab.closest(".fr-tabs") !== this.element) {
				return;
			}

			this.tabLinks.forEach((tabLink) => {
				const isActive = tabLink === newActiveTab;

				if (isActive) {
					tabLink.setAttribute("aria-selected", isActive);
					tabLink.setAttribute("tabindex", "0");
					tabLink.classList.toggle("active", isActive);
				} else {
					tabLink.removeAttribute("aria-selected", isActive);
					tabLink.setAttribute("tabindex", "-1");
					tabLink.classList.toggle("active", isActive);
				}
			});

			this.contentItems.forEach((contentItem, contentIndex) => {
				const isActiveContent = contentIndex === index;
				contentItem.classList.toggle("active", isActiveContent);
				contentItem.setAttribute("tabindex", isActiveContent ? "0" : "-1");
			});

			if (this.options.animate) {
				this.handleActiveLinkAnimation(newActiveTab);
			}
		}
	}

	// Initialize the Tabs component for each fr-tabs element on the page
	document.querySelectorAll(".fr-tabs").forEach((element) => {
		new Tabs(element);
	});

	// helpers

	function scrollTo(c, e, d) {
		d || (d = easeOutCuaic);
		var a = document.documentElement;
		if (0 === a.scrollTop) {
			var b = a.scrollTop;
			++a.scrollTop;
			a = b + 1 === a.scrollTop-- ? a : document.body;
		}
		b = a.scrollTop;
		0 >= e ||
			("object" === typeof b && (b = b.offsetTop),
			"object" === typeof c && (c = c.offsetTop),
			(function (a, b, c, f, d, e, h) {
				function g() {
					0 > f || 1 < f || 0 >= d
						? (a.scrollTop = c)
						: ((a.scrollTop = b - (b - c) * h(f)),
						  (f += d * e),
						  setTimeout(g, e));
				}
				g();
			})(a, b, c, 0, 1 / e, 20, d));
	}
	function easeOutCuaic(t) {
		t--;
		return t * t * t + 1;
	}
}

document.addEventListener("DOMContentLoaded", function (e) {
	bricksIsFrontend && tabs_script();
});
