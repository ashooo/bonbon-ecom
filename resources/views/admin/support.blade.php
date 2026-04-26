<div id="support-section" class="admin-section hidden">
    <div class="space-y-3">
        <div class="grid min-h-0 gap-6 xl:h-[calc(100vh-10rem)] 2xl:grid-cols-[340px,minmax(0,1fr),320px]">
            <aside class="flex min-h-0 flex-col overflow-hidden rounded-3xl bg-white shadow-soft">
                <div class="border-b border-slate-200 px-5 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Conversations</p>
                            <p id="support-conversation-count" class="text-xs text-slate-500">Loading inbox...</p>
                        </div>
                        <span id="support-admin-online" class="rounded-full bg-slate-200 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600">Live</span>
                    </div>
                </div>
                <div id="support-conversation-list" class="min-h-0 flex-1 overflow-y-auto p-3">
                    <div class="rounded-3xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">No conversations yet.</div>
                </div>
            </aside>

            <div class="flex min-h-0 min-w-0 flex-col overflow-hidden rounded-3xl bg-white shadow-soft">
                <div class="shrink-0 border-b border-slate-200 bg-gradient-to-r from-white via-pink-50 to-white px-6 py-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            <span id="support-selected-avatar" class="relative flex h-12 w-12 items-center justify-center rounded-full bg-pink-100 text-sm font-semibold text-[#5A3A3A]">--</span>
                            <div>
                                <h2 id="support-selected-name" class="text-xl font-semibold text-slate-800">Select a conversation</h2>
                                <p id="support-selected-meta" class="text-sm text-slate-500">Customer details will appear here.</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span id="support-selected-presence" class="rounded-full bg-slate-200 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600">Offline</span>
                        </div>
                    </div>
                </div>

                <div id="support-thread" class="min-h-0 flex-1 overflow-y-auto overscroll-contain bg-[linear-gradient(180deg,_#fffdfd,_#fff6f7)] px-4 py-6 sm:px-6">
                    <div id="support-thread-empty" class="mx-auto max-w-md rounded-[2rem] border border-dashed border-[#E9CBD3] bg-white/75 px-6 py-8 text-center text-sm leading-7 text-[#8C6770]">
                        Pick a conversation from the left to open the message history.
                    </div>
                </div>

                <div class="shrink-0 border-t border-slate-200 bg-white px-4 py-4 sm:px-6">
                    <div id="support-typing-indicator" class="mb-3 hidden text-sm text-slate-500">Customer is typing...</div>

                    <form id="support-send-form" class="space-y-3">
                        <div id="support-attachment-preview" class="hidden rounded-[1.25rem] border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div id="support-attachment-preview-content" class="min-w-0 flex-1"></div>
                                <button id="support-attachment-clear" type="button" class="rounded-full p-2 text-slate-500 transition hover:bg-white">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-end gap-3 rounded-[1.75rem] border border-slate-200 bg-slate-50 p-3">
                            <label class="inline-flex h-12 w-12 cursor-pointer items-center justify-center rounded-full bg-white text-slate-700 shadow-sm ring-1 ring-slate-200 transition hover:bg-pink-50">
                                <input id="support-attachment" name="attachment" type="file" class="hidden" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 13-5.172 5.172a4 4 0 1 1-5.656-5.656L13.5 6.187A2 2 0 0 1 16.328 9l-6.364 6.364a.75.75 0 1 1-1.06-1.06l5.657-5.657" />
                                </svg>
                            </label>

                            <div class="min-w-[220px] flex-1">
                                <textarea id="support-message-input" name="body" rows="1" class="max-h-40 w-full resize-none bg-transparent px-2 py-3 text-sm text-slate-800 outline-none placeholder:text-slate-400" placeholder="Reply to the customer..."></textarea>
                            </div>

                            <button type="submit" class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#5A3A3A] text-white transition hover:bg-[#7A5252]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-6-6 6 6-6 6" />
                                </svg>
                            </button>
                        </div>
                        <p id="support-upload-name" class="px-2 text-xs text-slate-500">Images, PDF, DOC, DOCX up to 5MB</p>
                    </form>
                </div>
            </div>

            <aside class="min-h-0">
                <div class="flex h-full min-h-0 flex-col overflow-hidden rounded-3xl bg-white shadow-soft">
                    <div class="shrink-0 border-b border-slate-200 px-5 py-5">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-800">Replies</p>
                            <span id="support-auto-reply-count" class="text-xs text-slate-500">0 total</span>
                        </div>
                        <div class="mt-4 grid grid-cols-2 rounded-2xl bg-slate-100 p-1">
                            <button type="button" class="support-tab-button rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition" data-tab="auto-replies" aria-selected="true">Auto-replies</button>
                            <button type="button" class="support-tab-button rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 transition" data-tab="saved-replies" aria-selected="false">Saved replies</button>
                        </div>
                    </div>

                    <div class="min-h-0 flex-1">
                        <div id="support-tab-auto-replies" class="support-tab-panel flex h-full min-h-0 flex-col px-5 py-5">
                            <p class="shrink-0 text-xs leading-6 text-slate-500">Use one offline reply and optional keyword replies like shipping or price.</p>
                            <form id="support-auto-reply-form" class="mt-4 min-h-0 flex-1 space-y-3 overflow-y-auto pr-1">
                                <input type="hidden" name="id" id="support-auto-reply-id">
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Trigger</label>
                                    <select id="support-auto-reply-trigger" name="trigger_type" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none">
                                        <option value="offline">Offline</option>
                                        <option value="keyword">Keyword</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Keyword</label>
                                    <input id="support-auto-reply-keyword" name="keyword" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none" placeholder="shipping">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Reply</label>
                                    <textarea id="support-auto-reply-text" name="reply_text" rows="5" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 outline-none" placeholder="Thanks for messaging Bonbon..."></textarea>
                                </div>
                                <button type="submit" class="w-full rounded-2xl bg-[#5A3A3A] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#7A5252]">Save auto-reply</button>
                            </form>
                        </div>

                        <div id="support-tab-saved-replies" class="support-tab-panel hidden h-full min-h-0 px-5 py-5">
                            <div id="support-auto-reply-list" class="h-full space-y-3 overflow-y-auto pr-1">
                                <div class="rounded-3xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">No auto-replies saved yet.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const section = document.getElementById('support-section');
        if (!section) {
            return;
        }

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const headers = {
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        };
        const buildHeaders = (json = false) => {
            const socketId = window.Echo?.socketId?.();

            return {
                ...headers,
                ...(json ? { 'Content-Type': 'application/json' } : {}),
                ...(socketId ? { 'X-Socket-ID': socketId } : {}),
            };
        };
        const escapeHtml = (value) => {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        };

        const listRoot = document.getElementById('support-conversation-list');
        const countRoot = document.getElementById('support-conversation-count');
        const threadRoot = document.getElementById('support-thread');
        const threadEmpty = document.getElementById('support-thread-empty');
        const selectedName = document.getElementById('support-selected-name');
        const selectedMeta = document.getElementById('support-selected-meta');
        const selectedAvatar = document.getElementById('support-selected-avatar');
        const selectedPresence = document.getElementById('support-selected-presence');
        const adminOnlinePill = document.getElementById('support-admin-online');
        const typingIndicator = document.getElementById('support-typing-indicator');
        const sendForm = document.getElementById('support-send-form');
        const messageInput = document.getElementById('support-message-input');
        const attachmentInput = document.getElementById('support-attachment');
        const attachmentPreview = document.getElementById('support-attachment-preview');
        const attachmentPreviewContent = document.getElementById('support-attachment-preview-content');
        const attachmentClear = document.getElementById('support-attachment-clear');
        const uploadName = document.getElementById('support-upload-name');
        const autoReplyForm = document.getElementById('support-auto-reply-form');
        const autoReplyList = document.getElementById('support-auto-reply-list');
        const autoReplyCount = document.getElementById('support-auto-reply-count');
        const tabButtons = document.querySelectorAll('.support-tab-button');
        const tabPanels = document.querySelectorAll('.support-tab-panel');
        let selectedConversationId = Number(new URLSearchParams(window.location.search).get('conversation')) || null;
        let pollTimer = null;
        let typingTimer = null;
        let typingState = false;
        let messagesHash = '';
        let currentMessages = [];
        let heartbeatTimer = null;
        let currentConversationChannel = null;
        let inboxSubscribed = false;
        let isSwitchingConversation = false;

        const routes = {
            data: @json(route('admin.chat.data')),
            sendBase: @json(url('/admin/chats')),
            autoReplies: @json(route('admin.chat.auto-replies.upsert')),
            presence: @json(route('admin.chat.presence')),
        };

        const isNearBottom = () => threadRoot.scrollHeight - threadRoot.scrollTop - threadRoot.clientHeight < 120;
        const scrollToBottom = () => {
            threadRoot.scrollTop = threadRoot.scrollHeight;
        };

        const scheduleRefresh = (delay = 10000) => {
            window.clearTimeout(pollTimer);
            pollTimer = window.setTimeout(fetchInbox, delay);
        };

        const subscribeToInbox = () => {
            if (inboxSubscribed || !window.Echo) {
                return;
            }

            inboxSubscribed = true;
            window.Echo.channel('chat.admin.inbox').listen('.chat.conversation.updated', (event) => {
                const isCurrentConversation = Number(event.conversation_id) === selectedConversationId;
                fetchInbox(isCurrentConversation, false);
            });
        };

        const subscribeToConversation = (channelName) => {
            if (!window.Echo || !channelName || currentConversationChannel === channelName) {
                return;
            }

            if (currentConversationChannel) {
                window.Echo.leave(currentConversationChannel);
            }

            currentConversationChannel = channelName;
            window.Echo.channel(channelName).listen('.chat.conversation.updated', () => {
                fetchInbox(true, false);
            });
        };

        const startHeartbeat = () => {
            if (heartbeatTimer) {
                return;
            }

            heartbeatTimer = window.setInterval(() => {
                const url = selectedConversationId
                    ? `${routes.presence.replace(/\/$/, '')}/${selectedConversationId}`
                    : routes.presence;

                fetch(url, {
                    method: 'POST',
                    headers: buildHeaders(true),
                    body: JSON.stringify({}),
                });
            }, 30000);
        };

        const renderComposerAttachment = (file) => {
            if (!file) {
                attachmentPreview.classList.add('hidden');
                attachmentPreviewContent.innerHTML = '';
                uploadName.textContent = 'Images, PDF, DOC, DOCX up to 5MB';
                return;
            }

            uploadName.textContent = file.name;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = () => {
                    attachmentPreviewContent.innerHTML = `
                        <div class="flex items-start gap-3">
                            <img src="${reader.result}" alt="${escapeHtml(file.name)}" class="h-20 w-20 rounded-2xl object-cover">
                            <div class="min-w-0 pt-1">
                                <p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(file.name)}</p>
                                <p class="mt-1 text-xs text-slate-500">Image ready to send</p>
                            </div>
                        </div>
                    `;
                    attachmentPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
                return;
            }

            attachmentPreviewContent.innerHTML = `
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white text-xs font-semibold text-slate-800 shadow-sm ring-1 ring-slate-200">FILE</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(file.name)}</p>
                        <p class="mt-1 text-xs text-slate-500">Attachment ready to send</p>
                    </div>
                </div>
            `;
            attachmentPreview.classList.remove('hidden');
        };

        const renderAutoReplies = (replies) => {
            autoReplyCount.textContent = `${replies.length} total`;

            if (!replies.length) {
                autoReplyList.innerHTML = '<div class="rounded-3xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">No auto-replies saved yet.</div>';
                return;
            }

            autoReplyList.innerHTML = replies.map((reply) => `
                <div class="support-auto-reply-card overflow-hidden rounded-3xl border border-slate-200 bg-slate-50" data-reply='${escapeHtml(JSON.stringify(reply))}'>
                    <button type="button" class="support-auto-reply-toggle flex w-full items-start justify-between gap-3 px-4 py-4 text-left" aria-expanded="false">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(reply.keyword || 'General offline fallback')}</p>
                            <p class="mt-2 text-xs uppercase tracking-[0.16em] text-slate-400">${escapeHtml(reply.trigger_type)}</p>
                        </div>
                        <span class="support-auto-reply-icon inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm transition-transform">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6" />
                            </svg>
                        </span>
                    </button>
                    <div class="support-auto-reply-body hidden border-t border-slate-200 px-4 py-4">
                        <p class="whitespace-pre-wrap text-sm leading-6 text-slate-600">${escapeHtml(reply.reply_text)}</p>
                        <div class="mt-4 flex items-center justify-end gap-2">
                            <button type="button" class="support-auto-reply-edit inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#E7C7CF] bg-white text-[#B77B85] transition hover:bg-[#FFF4F6]" aria-label="Edit reply" title="Edit reply">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20h4l10-10a2.121 2.121 0 0 0-3-3L5 17v3Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m13.5 6.5 4 4" />
                                </svg>
                            </button>
                            <button type="button" class="support-auto-reply-delete inline-flex h-10 w-10 items-center justify-center rounded-full bg-rose-500 text-white transition hover:bg-rose-600" data-id="${reply.id}" aria-label="Delete reply" title="Delete reply">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 6h18" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 6V4h8v2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 6l-1 14H6L5 6" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 11v6M14 11v6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            autoReplyList.querySelectorAll('.support-auto-reply-toggle').forEach((button) => {
                button.addEventListener('click', () => {
                    const card = button.closest('.support-auto-reply-card');
                    const body = card.querySelector('.support-auto-reply-body');
                    const icon = card.querySelector('.support-auto-reply-icon');
                    const expanded = button.getAttribute('aria-expanded') === 'true';

                    button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                    body.classList.toggle('hidden', expanded);
                    icon.classList.toggle('rotate-180', !expanded);
                });
            });

            autoReplyList.querySelectorAll('.support-auto-reply-edit').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const card = button.closest('.support-auto-reply-card');
                    const reply = JSON.parse(card.dataset.reply);
                    document.getElementById('support-auto-reply-id').value = reply.id;
                    document.getElementById('support-auto-reply-trigger').value = reply.trigger_type;
                    document.getElementById('support-auto-reply-keyword').value = reply.keyword ?? '';
                    document.getElementById('support-auto-reply-text').value = reply.reply_text;

                    const autoRepliesTabButton = document.querySelector('[data-tab="auto-replies"]');
                    autoRepliesTabButton?.click();
                    autoReplyForm.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                });
            });

            autoReplyList.querySelectorAll('.support-auto-reply-delete').forEach((button) => {
                button.addEventListener('click', async (event) => {
                    event.stopPropagation();
                    const response = await fetch(`${routes.sendBase}/auto-replies/${button.dataset.id}`, {
                        method: 'DELETE',
                        headers: buildHeaders(),
                    });

                    if (response.ok) {
                        fetchInbox();
                    }
                });
            });
        };

        tabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.dataset.tab;

                tabButtons.forEach((tabButton) => {
                    const active = tabButton === button;
                    tabButton.setAttribute('aria-selected', active ? 'true' : 'false');
                    tabButton.classList.toggle('bg-white', active);
                    tabButton.classList.toggle('text-slate-800', active);
                    tabButton.classList.toggle('shadow-sm', active);
                    tabButton.classList.toggle('text-slate-600', !active);
                });

                tabPanels.forEach((panel) => {
                    panel.classList.toggle('hidden', panel.id !== `support-tab-${target}`);
                });
            });
        });

        const renderConversations = (conversations) => {
            countRoot.textContent = `${conversations.length} conversation${conversations.length === 1 ? '' : 's'}`;

            if (!conversations.length) {
                listRoot.innerHTML = '<div class="rounded-3xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500">No conversations yet.</div>';
                return;
            }

            if (!selectedConversationId) {
                selectedConversationId = conversations[0].id;
            }

            listRoot.innerHTML = conversations.map((conversation) => {
                const active = conversation.id === selectedConversationId;
                return `
                    <button type="button" data-conversation-id="${conversation.id}" class="support-conversation-item mb-2 w-full rounded-[1.5rem] border px-4 py-4 text-left transition ${active ? 'border-pink-200 bg-pink-50' : 'border-transparent bg-white hover:border-slate-200 hover:bg-slate-50'}">
                        <div class="flex items-start gap-3">
                            <span class="relative flex h-12 w-12 items-center justify-center rounded-full bg-pink-100 text-sm font-semibold text-[#5A3A3A]">
                                ${escapeHtml(conversation.customer_initials)}
                                <span class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white ${conversation.customer_is_online ? 'bg-emerald-500' : 'bg-slate-300'}"></span>
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(conversation.customer_name)}</p>
                                        <p class="truncate text-xs text-slate-500">${escapeHtml(conversation.customer_email ?? 'Guest visitor')}</p>
                                    </div>
                                    ${conversation.unread_count ? `<span class="rounded-full bg-[#5A3A3A] px-2 py-1 text-[11px] font-semibold text-white">${conversation.unread_count}</span>` : ''}
                                </div>
                                <p class="mt-2 truncate text-sm text-slate-600">${escapeHtml(conversation.last_message_preview)}</p>
                                <p class="mt-2 text-[11px] uppercase tracking-[0.16em] text-slate-400">${escapeHtml(conversation.last_message_time ?? 'Just now')}</p>
                            </div>
                        </div>
                    </button>
                `;
            }).join('');

        };

        listRoot.addEventListener('click', (event) => {
            const item = event.target.closest('.support-conversation-item');

            if (!item) {
                return;
            }

            const nextConversationId = Number(item.dataset.conversationId);

            if (!nextConversationId || nextConversationId === selectedConversationId || isSwitchingConversation) {
                return;
            }

            isSwitchingConversation = true;
            selectedConversationId = nextConversationId;
            messagesHash = '';
            currentMessages = [];
            window.clearTimeout(pollTimer);
            fetchInbox().finally(() => {
                isSwitchingConversation = false;
            });
        });

        const renderSelectedConversation = (conversation) => {
            if (!conversation) {
                selectedName.textContent = 'Select a conversation';
                selectedMeta.textContent = 'Customer details will appear here.';
                selectedAvatar.textContent = '--';
                selectedPresence.textContent = 'Offline';
                return;
            }

            selectedAvatar.textContent = conversation.customer_initials;
            selectedName.textContent = conversation.customer_name;
            selectedMeta.textContent = conversation.customer_email
                ? `${conversation.customer_email} • Started ${conversation.meta.created_at}`
                : `Guest shopper • Started ${conversation.meta.created_at}`;
            selectedPresence.textContent = conversation.customer_is_online ? 'Online' : 'Offline';
            selectedPresence.className = `rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] ${conversation.customer_is_online ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'}`;
            typingIndicator.classList.toggle('hidden', !conversation.customer_is_typing);
        };

        const renderMessages = (messages) => {
            currentMessages = messages;
            const nextHash = JSON.stringify(messages.map((message) => [message.id, message.receipt_label]));
            const stickBottom = isNearBottom();

            if (!messages.length) {
                threadRoot.innerHTML = '';
                threadRoot.appendChild(threadEmpty);
                messagesHash = nextHash;
                return;
            }

            if (messagesHash === nextHash && threadRoot.querySelector('[data-message-id]')) {
                return;
            }

            threadRoot.innerHTML = messages.map((message) => {
                if (message.sender_type === 'system') {
                    return `
                        <div data-message-id="${message.id}" class="mb-5 flex justify-center">
                            <div class="max-w-xl rounded-full bg-pink-50 px-4 py-2 text-center text-xs font-medium text-slate-500">
                                ${escapeHtml(message.body ?? '')}
                            </div>
                        </div>
                    `;
                }

                const wrapper = message.is_mine ? 'justify-end' : 'justify-start';
                const bubble = message.is_mine ? 'bg-[#5A3A3A] text-white' : 'border border-slate-200 bg-white text-slate-800';
                const meta = message.is_mine ? 'text-white/70' : 'text-slate-400';
                let attachment = '';
                if (message.attachment_url) {
                    attachment = message.is_image
                        ? `<div class="mt-3 overflow-hidden rounded-2xl bg-white/70"><a href="${message.attachment_view_url || message.attachment_url}" target="_blank" class="block"><img src="${message.attachment_view_url || message.attachment_url}" alt="${escapeHtml(message.attachment_name ?? 'Attachment')}" class="max-h-72 w-full object-cover"></a><div class="flex items-center justify-end border-t border-white/60 px-3 py-2"><a href="${message.attachment_download_url || message.attachment_url}" download class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-[#5A3A3A] shadow-sm transition hover:bg-[#FFF0F3]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" /></svg></a></div></div>`
                        : `<div class="mt-3 flex items-center gap-3 rounded-2xl border border-white/50 bg-white/75 px-4 py-3 text-sm font-semibold text-[#5A3A3A]"><span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-pink-100">FILE</span><span class="truncate flex-1">${escapeHtml(message.attachment_name ?? 'Download attachment')}</span><a href="${message.attachment_download_url || message.attachment_url}" download class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-[#5A3A3A] shadow-sm transition hover:bg-[#FFF0F3]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" /></svg></a></div>`;
                }

                return `
                    <div data-message-id="${message.id}" class="mb-4 flex ${wrapper}">
                        <div class="max-w-[82%] sm:max-w-[72%]">
                            <div class="${bubble} ${message.is_mine ? 'rounded-[1.8rem_1.8rem_0.5rem_1.8rem]' : 'rounded-[1.8rem_1.8rem_1.8rem_0.5rem]'} px-4 py-3 shadow-sm">
                                ${message.body ? `<div class="whitespace-pre-wrap text-sm leading-7">${escapeHtml(message.body)}</div>` : ''}
                                ${attachment}
                            </div>
                            <div class="mt-1 flex ${message.is_mine ? 'justify-end' : 'justify-start'} gap-2 px-1 text-[11px] ${meta}">
                                <span>${escapeHtml(message.timestamp)}</span>
                                ${message.is_mine ? `<span>${escapeHtml(message.receipt_label)}</span>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            messagesHash = nextHash;

            if (stickBottom) {
                scrollToBottom();
            }
        };

        const fetchInbox = async () => {
            try {
                window.clearTimeout(pollTimer);
                const url = new URL(routes.data, window.location.origin);
                if (selectedConversationId) {
                    url.searchParams.set('conversation', selectedConversationId);
                }

                const response = await fetch(url, { headers: buildHeaders() });
                const payload = await response.json();
                renderConversations(payload.conversations);
                renderSelectedConversation(payload.selected_conversation);
                renderMessages(payload.messages);
                renderAutoReplies(payload.auto_replies ?? []);
                subscribeToInbox();

                if (payload.selected_conversation?.broadcast_channel) {
                    subscribeToConversation(payload.selected_conversation.broadcast_channel);
                }

                const online = payload.selected_conversation?.admin_is_online ?? false;
                adminOnlinePill.textContent = 'Live';
                adminOnlinePill.className = `rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] ${online ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'}`;
            } finally {
                scheduleRefresh();
            }
        };

        const sendTyping = async (isTyping) => {
            if (!selectedConversationId || typingState === isTyping) {
                return;
            }

            typingState = isTyping;
            await fetch(`${routes.sendBase}/${selectedConversationId}/typing`, {
                method: 'POST',
                headers: buildHeaders(true),
                body: JSON.stringify({ is_typing: isTyping }),
            });
        };

        sendForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!selectedConversationId) {
                uploadName.textContent = 'Pick a conversation first.';
                return;
            }

            const formData = new FormData(sendForm);

            try {
                const response = await fetch(`${routes.sendBase}/${selectedConversationId}/messages`, {
                    method: 'POST',
                    headers: buildHeaders(),
                    body: formData,
                });

                if (!response.ok) {
                    throw new Error('Message failed.');
                }

                const payload = await response.json();

                messageInput.value = '';
                attachmentInput.value = '';
                renderComposerAttachment(null);
                messageInput.style.height = 'auto';
                typingState = false;
                void sendTyping(false);

                if (payload.conversation) {
                    renderSelectedConversation(payload.conversation);
                }

                renderMessages([...currentMessages, payload.message]);
                scrollToBottom();
                scheduleRefresh(250);
            } catch (error) {
                uploadName.textContent = 'Message failed to send. Please try again.';
            }
        });

        attachmentInput.addEventListener('change', () => {
            renderComposerAttachment(attachmentInput.files[0] ?? null);
        });

        attachmentClear.addEventListener('click', () => {
            attachmentInput.value = '';
            renderComposerAttachment(null);
        });

        messageInput.addEventListener('input', () => {
            messageInput.style.height = 'auto';
            messageInput.style.height = `${Math.min(messageInput.scrollHeight, 160)}px`;
            void sendTyping(messageInput.value.trim().length > 0);

            window.clearTimeout(typingTimer);
            typingTimer = window.setTimeout(() => {
                void sendTyping(false);
            }, 1800);
        });

        messageInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendForm.requestSubmit();
            }
        });

        autoReplyForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(autoReplyForm);

            const response = await fetch(routes.autoReplies, {
                method: 'POST',
                headers: buildHeaders(true),
                body: JSON.stringify({
                    id: formData.get('id') || null,
                    trigger_type: formData.get('trigger_type'),
                    keyword: formData.get('keyword'),
                    reply_text: formData.get('reply_text'),
                    is_active: true,
                    sort_order: 0,
                }),
            });

            if (response.ok) {
                autoReplyForm.reset();
                document.getElementById('support-auto-reply-id').value = '';
                fetchInbox();
            }
        });

        startHeartbeat();
        fetchInbox();
    });
</script>
@endpush
