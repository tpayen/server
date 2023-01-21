<!--
  - @copyright 2023 Christopher Ng <chrng8@gmail.com>
  -
  - @author Christopher Ng <chrng8@gmail.com>
  -
  - @license AGPL-3.0-or-later
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program. If not, see <http://www.gnu.org/licenses/>.
  -
-->

<template>
	<NcHeaderMenu id="settings"
		class="user-menu"
		:aria-label="t('core', 'Open settings menu')"
		@open="handleOpen">
		<template #trigger>
			<NcAvatar class="user-menu__avatar"
				:disable-menu="true"
				:disable-tooltip="true"
				:size="32"
				:user="userId"
			/>
		</template>
		<nav class="user-menu__nav">
			<ul>
				<UserMenuEntry v-for="entry in settingsNavEntries"
					v-bind="entry"
					:key="entry.id" />
			</ul>
		</nav>
	</NcHeaderMenu>
</template>

<script>
import { generateUrl } from '@nextcloud/router'
import { getCurrentUser } from '@nextcloud/auth'
import { loadState } from '@nextcloud/initial-state'
import { emit } from '@nextcloud/event-bus'

import NcAvatar from '@nextcloud/vue/dist/Components/NcAvatar.js'
import NcHeaderMenu from '@nextcloud/vue/dist/Components/NcHeaderMenu.js'

import UserMenuEntry from '../components/UserMenu/UserMenuEntry.vue'

const user = getCurrentUser()

const getAvatar = (size) => {
	return generateUrl('/avatar/{userId}/{size}', { userId: user?.uid, size })
}

const settingsNavEntries = loadState('core', 'settingsNavEntries')
console.log(JSON.stringify(settingsNavEntries, null, 2))

export default {
	name: 'UserMenu',

	components: {
		NcAvatar,
		NcHeaderMenu,
		UserMenuEntry,
	},

	data() {
		return {
			userId: getCurrentUser()?.uid,
			settingsNavEntries,
		}
	},

	mounted() {
		emit('core:user-menu:mounted')
	},

	methods: {
		handleOpen() {

		},
	},
}
</script>

<style lang="scss" scoped>
.user-menu {
	--header-menu-entry-height: 44px;

	// TODO Extend API of NcHeaderMenu or keep these overrides?
	:deep {
		.header-menu {
			&__trigger {
				opacity: 1 !important;
			}
			&__carret {
				display: none !important;
			}
			&__wrapper {
				max-width: 250px !important;
				width: 250px !important;
			}
			&__content {
				max-width: 100% !important;
				width: 100% !important;
			}
		}
	}

	&__avatar {
		&:active,
		&:focus,
		&:hover {
			border: 2px solid var(--color-primary-text);
		}
	}

	&__nav {
		display: flex;
		width: 100%;

		ul {
			display: flex;
			flex-direction: column;
			gap: 2px;

			&:deep {
				li {
					a {
						border-radius: 6px;
						display: inline-flex;
						align-items: center;
						height: var(--header-menu-entry-height);
						color: var(--color-main-text);
						padding: 10px 8px;
						box-sizing: border-box;
						white-space: nowrap;
						position: relative;
						width: 100%;

						&:hover {
							background-color: var(--color-background-hover);
						}

						&:focus-visible {
							background-color: var(--color-background-hover);
							box-shadow: inset 0 0 0 2px var(--color-primary);
							outline: none;
						}

						&:active,
						&.active {
							background-color: var(--color-primary-light);
						}

						span {
							padding-bottom: 0;
							color: var(--color-main-text);
							white-space: nowrap;
							overflow: hidden;
							text-overflow: ellipsis;
							max-width: 110px;
						}

						img {
							width: 16px;
							height: 16px;
							margin-right: 10px;
						}

						img,
						svg {
							opacity: .7;
							filter: var(--background-invert-if-dark);
						}
					}
				}
			}
		}
	}
}
</style>
