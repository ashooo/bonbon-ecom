@extends('layouts.app')

@section('content')
    <section class="mx-auto max-w-[1500px]">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-[#C88A92]">Admin Inbox</p>
                <h1 class="mt-2 text-3xl font-semibold text-[#5A3A3A]">Bonbon Chat Dashboard</h1>
                <p class="mt-2 text-sm text-[#7A5252]">Monitor every support conversation, reply in real time, and manage auto-replies from one place.</p>
            </div>
            <a href="{{ route('assistant') }}" class="rounded-full border border-[#EACED5] bg-white px-5 py-3 text-sm font-semibold text-[#5A3A3A] transition hover:bg-[#FFF6F8]">Open customer chat view</a>
        </div>

        <div class="grid gap-6 xl:grid-cols-[340px,minmax(0,1fr),340px]">
            <aside class="overflow-hidden rounded-[2rem] border border-[#F1DADF] bg-white shadow-[0_20px_45px_rgba(90,58,58,0.12)]">
                <div class="border-b border-[#F4E2E6] px-5 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-[#5A3A3A]">Conversations</p>
                            <p id="conversation-count" class="text-xs text-[#A07C84]">Loading inbox...</p>
                        </div>
                        <span id="admin-online-pill" class="rounded-full bg-slate-200 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600">Offline</span>
                    </div>
                </div>
                <div id="conversation-list" class="max-h-[72vh] overflow-y-auto p-3">
                    <div class="rounded-[1.5rem] border border-dashed border-[#EBCED5] px-4 py-8 text-center text-sm text-[#8C6770]">No conversations yet.</div>
                </div>
            </aside>

            <div class="overflow-hidden rounded-[2rem] border border-[#F1DADF] bg-white shadow-[0_20px_45px_rgba(90,58,58,0.12)]">
                <div class="border-b border-[#F4E2E6] bg-[linear-gradient(135deg,_#fff7f8,_#fff,_#fdf2f4)] px-6 py-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            <span id="selected-avatar" class="flex h-12 w-12 items-center justify-center rounded-full bg-[#F7E3E7] text-sm font-semibold text-[#5A3A3A]">--</span>
                            <div>
                                <h2 id="selected-name" class="text-xl font-semibold text-[#5A3A3A]">Select a conversation</h2>
                                <p id="selected-meta" class="text-sm text-[#8C6770]">Customer details and live status will appear here.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span id="selected-status" class="rounded-full bg-slate-200 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600">Waiting</span>
                            <span id="selected-presence" class="rounded-full bg-slate-200 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600">Offline</span>
                        </div>
                    </div>
                </div>

                <div id="admin-thread" class="h-[58vh] overflow-y-auto bg-[linear-gradient(180deg,_#fffdfd,_#fff6f7)] px-4 py-6 sm:px-6">
                    <div id="admin-thread-empty" class="mx-auto max-w-md rounded-[2rem] border border-dashed border-[#E9CBD3] bg-white/75 px-6 py-8 text-center text-sm leading-7 text-[#8C6770]">
                        Pick a conversation from the left to open the message history.
                    </div>
                </div>

                <div class="border-t border-[#F4E2E6] bg-white px-4 py-4 sm:px-6">
                    <div id="admin-typing-indicator" class="mb-3 hidden text-sm text-[#9E7680]">Customer is typing...</div>

                    <form id="admin-send-form" class="space-y-3">
                        <div class="flex flex-wrap items-end gap-3 rounded-[1.75rem] border border-[#EED9DE] bg-[#FFF9FA] p-3">
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-white px-4 py-3 text-sm font-semibold text-[#6B4951] shadow-sm ring-1 ring-[#F1DADF] transition hover:bg-[#FFF3F5]">
                                <input id="admin-attachment" name="attachment" type="file" class="hidden" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m18 13-5.172 5.172a4 4 0 1 1-5.656-5.656L13.5 6.187A2 2 0 0 1 16.328 9l-6.364 6.364a.75.75 0 1 1-1.06-1.06l5.657-5.657" />
                                </svg>
                                Attach
                            </label>

                            <div class="min-w-[220px] flex-1">
                                <textarea id="admin-message-input" name="body" rows="1" class="max-h-40 w-full resize-none bg-transparent px-2 py-3 text-sm text-[#5A3A3A] outline-none placeholder:text-[#B28D95]" placeholder="Reply to the customer..."></textarea>
                            </div>

                            <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#5A3A3A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#7A5252]">
                                Send
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-6-6 6 6-6 6" />
                                </svg>
                            </button>
                        </div>
                        <p id="admin-upload-name" class="px-2 text-xs text-[#9E7680]">Images, PDF, DOC, DOCX up to 5MB</p>
                    </form>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-[2rem] border border-[#F1DADF] bg-white p-5 shadow-[0_20px_45px_rgba(90,58,58,0.12)]">
                    <p class="text-sm font-semibold text-[#5A3A3A]">Auto-replies</p>
                    <p class="mt-1 text-xs leading-6 text-[#8C6770]">Use one offline reply and optional keyword-based responses like shipping, price, or custom.</p>

                    <form id="auto-reply-form" class="mt-4 space-y-3">
                        <input type="hidden" name="id" id="auto-reply-id">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#8C6770]">Trigger</label>
                            <select id="auto-reply-trigger" name="trigger_type" class="w-full rounded-2xl border border-[#EED9DE] bg-[#FFF9FA] px-4 py-3 text-sm text-[#5A3A3A] outline-none">
                                <option value="offline">Offline</option>
                                <option value="keyword">Keyword</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#8C6770]">Keyword</label>
                            <input id="auto-reply-keyword" name="keyword" type="text" class="w-full rounded-2xl border border-[#EED9DE] bg-[#FFF9FA] px-4 py-3 text-sm text-[#5A3A3A] outline-none" placeholder="shipping">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.18em] text-[#8C6770]">Reply</label>
                            <textarea id="auto-reply-text" name="reply_text" rows="4" class="w-full rounded-2xl border border-[#EED9DE] bg-[#FFF9FA] px-4 py-3 text-sm text-[#5A3A3A] outline-none" placeholder="Thanks for messaging Bonbon..."></textarea>
                        </div>
                        <button type="submit" class="w-full rounded-2xl bg-[#5A3A3A] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#7A5252]">Save auto-reply</button>
                    </form>
                </div>

                <div class="rounded-[2rem] border border-[#F1DADF] bg-white p-5 shadow-[0_20px_45px_rgba(90,58,58,0.12)]">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-[#5A3A3A]">Saved replies</p>
                        <span class="text-xs text-[#A07C84]">{{ $autoReplies->count() }} total</span>
                    </div>
                    <div id="auto-reply-list" class="mt-4 space-y-3">
                        @forelse ($autoReplies as $reply)
                            <div class="auto-reply-card rounded-[1.5rem] border border-[#F2DEE2] bg-[#FFF9FA] p-4" data-reply='@json($reply)'>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="rounded-full bg-[#F8E3E7] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#9D6E77]">{{ $reply->trigger_type }}</span>
                                    <button type="button" class="auto-reply-delete text-xs font-semibold uppercase tracking-[0.16em] text-rose-500" data-id="{{ $reply->id }}">Delete</button>
                                </div>
                                <p class="mt-3 text-sm font-semibold text-[#5A3A3A]">{{ $reply->keyword ?: 'General offline fallback' }}</p>
                                <p class="mt-2 text-sm leading-6 text-[#7A5252]">{{ $reply->reply_text }}</p>
                                <button type="button" class="auto-reply-edit mt-3 text-xs font-semibold uppercase tracking-[0.16em] text-[#B77B85]">Edit</button>
                            </div>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-[#EBCED5] px-4 py-8 text-center text-sm text-[#8C6770]">No auto-replies saved yet.</div>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            const headers = {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            };
            const jsonHeaders = {
                ...headers,
                'Content-Type': 'application/json',
            };

            const listRoot = document.getElementById('conversation-list');
            const countRoot = document.getElementById('conversation-count');
            const threadRoot = document.getElementById('admin-thread');
            const threadEmpty = document.getElementById('admin-thread-empty');
            const selectedName = document.getElementById('selected-name');
            const selectedMeta = document.getElementById('selected-meta');
            const selectedAvatar = document.getElementById('selected-avatar');
            const selectedStatus = document.getElementById('selected-status');
            const selectedPresence = document.getElementById('selected-presence');
            const adminOnlinePill = document.getElementById('admin-online-pill');
            const typingIndicator = document.getElementById('admin-typing-indicator');
            const sendForm = document.getElementById('admin-send-form');
            const messageInput = document.getElementById('admin-message-input');
            const attachmentInput = document.getElementById('admin-attachment');
            const uploadName = document.getElementById('admin-upload-name');
            const autoReplyForm = document.getElementById('auto-reply-form');
            let selectedConversationId = @json($selectedConversation?->id);
            let pollTimer = null;
            let typingTimer = null;
            let typingState = false;
            let messagesHash = '';

            const routes = {
                data: @json(route('admin.chat.data')),
                sendBase: @json(url('/admin/chats')),
                autoReplies: @json(route('admin.chat.auto-replies.upsert')),
            };

            const escapeHtml = (value) => {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            };

            const isNearBottom = () => threadRoot.scrollHeight - threadRoot.scrollTop - threadRoot.clientHeight < 120;
            const scrollToBottom = () => {
                threadRoot.scrollTop = threadRoot.scrollHeight;
            };

            const renderConversations = (conversations) => {
                countRoot.textContent = `${conversations.length} conversation${conversations.length === 1 ? '' : 's'}`;

                if (!conversations.length) {
                    listRoot.innerHTML = '<div class="rounded-[1.5rem] border border-dashed border-[#EBCED5] px-4 py-8 text-center text-sm text-[#8C6770]">No conversations yet.</div>';
                    return;
                }

                if (!selectedConversationId) {
                    selectedConversationId = conversations[0].id;
                }

                listRoot.innerHTML = conversations.map((conversation) => {
                    const active = conversation.id === selectedConversationId;
                    return `
                        <button type="button" data-conversation-id="${conversation.id}" class="conversation-item mb-2 w-full rounded-[1.5rem] border px-4 py-4 text-left transition ${active ? 'border-[#D7A6B0] bg-[#FFF5F7]' : 'border-transparent bg-white hover:border-[#F1DADF] hover:bg-[#FFF9FA]'}">
                            <div class="flex items-start gap-3">
                                <span class="relative flex h-12 w-12 items-center justify-center rounded-full bg-[#F8E3E7] text-sm font-semibold text-[#5A3A3A]">
                                    ${escapeHtml(conversation.customer_initials)}
                                    <span class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white ${conversation.customer_is_online ? 'bg-emerald-500' : 'bg-slate-300'}"></span>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-[#5A3A3A]">${escapeHtml(conversation.customer_name)}</p>
                                            <p class="truncate text-xs text-[#9E7680]">${escapeHtml(conversation.customer_email ?? 'Guest visitor')}</p>
                                        </div>
                                        ${conversation.unread_count ? `<span class="rounded-full bg-[#5A3A3A] px-2 py-1 text-[11px] font-semibold text-white">${conversation.unread_count}</span>` : ''}
                                    </div>
                                    <p class="mt-2 truncate text-sm text-[#7A5252]">${escapeHtml(conversation.last_message_preview)}</p>
                                    <p class="mt-2 text-[11px] uppercase tracking-[0.16em] text-[#B38A93]">${escapeHtml(conversation.last_message_time ?? 'Just now')}</p>
                                </div>
                            </div>
                        </button>
                    `;
                }).join('');

                document.querySelectorAll('.conversation-item').forEach((item) => {
                    item.addEventListener('click', () => {
                        selectedConversationId = Number(item.dataset.conversationId);
                        messagesHash = '';
                        fetchInbox();
                    });
                });
            };

            const renderSelectedConversation = (conversation) => {
                if (!conversation) {
                    selectedName.textContent = 'Select a conversation';
                    selectedMeta.textContent = 'Customer details and live status will appear here.';
                    selectedAvatar.textContent = '--';
                    selectedStatus.textContent = 'Waiting';
                    selectedPresence.textContent = 'Offline';
                    return;
                }

                selectedAvatar.textContent = conversation.customer_initials;
                selectedName.textContent = conversation.customer_name;
                selectedMeta.textContent = conversation.customer_email
                    ? `${conversation.customer_email} • Started ${conversation.meta.created_at}`
                    : `Guest shopper • Started ${conversation.meta.created_at}`;
                selectedStatus.textContent = conversation.customer_is_typing ? 'Typing' : conversation.status;
                selectedStatus.className = `rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] ${conversation.customer_is_typing ? 'bg-amber-100 text-amber-700' : 'bg-[#F8E3E7] text-[#9D6E77]'}`;
                selectedPresence.textContent = conversation.customer_is_online ? 'Online' : 'Offline';
                selectedPresence.className = `rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] ${conversation.customer_is_online ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'}`;
                typingIndicator.classList.toggle('hidden', !conversation.customer_is_typing);
            };

            const renderMessages = (messages) => {
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
                                <div class="max-w-xl rounded-full bg-[#FCEDEF] px-4 py-2 text-center text-xs font-medium text-[#8C6770]">
                                    ${escapeHtml(message.body ?? '')}
                                </div>
                            </div>
                        `;
                    }

                    const wrapper = message.is_mine ? 'justify-end' : 'justify-start';
                    const bubble = message.is_mine
                        ? 'bg-[#5A3A3A] text-white'
                        : 'border border-[#F0DCE1] bg-white text-[#5A3A3A]';
                    const meta = message.is_mine ? 'text-white/70' : 'text-[#A07C84]';

                    let attachment = '';
                    if (message.attachment_url) {
                        attachment = message.is_image
                            ? `<a href="${message.attachment_url}" target="_blank" class="mt-3 block overflow-hidden rounded-2xl bg-white/70"><img src="${message.attachment_url}" alt="${escapeHtml(message.attachment_name ?? 'Attachment')}" class="max-h-72 w-full object-cover"></a>`
                            : `<a href="${message.attachment_url}" target="_blank" class="mt-3 flex items-center gap-3 rounded-2xl border border-white/50 bg-white/75 px-4 py-3 text-sm font-semibold text-[#5A3A3A]"><span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#F8E4E8]">FILE</span><span class="truncate">${escapeHtml(message.attachment_name ?? 'Download attachment')}</span></a>`;
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
                    const url = new URL(routes.data, window.location.origin);
                    if (selectedConversationId) {
                        url.searchParams.set('conversation', selectedConversationId);
                    }

                    const response = await fetch(url, { headers });
                    const payload = await response.json();
                    renderConversations(payload.conversations);
                    renderSelectedConversation(payload.selected_conversation);
                    renderMessages(payload.messages);

                    const online = payload.selected_conversation?.admin_is_online ?? false;
                    adminOnlinePill.textContent = online ? 'Live' : 'Offline';
                    adminOnlinePill.className = `rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] ${online ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'}`;
                } finally {
                    pollTimer = window.setTimeout(fetchInbox, 2500);
                }
            };

            const sendTyping = async (isTyping) => {
                if (!selectedConversationId || typingState === isTyping) {
                    return;
                }

                typingState = isTyping;
                await fetch(`${routes.sendBase}/${selectedConversationId}/typing`, {
                    method: 'POST',
                    headers: jsonHeaders,
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
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error('Message failed.');
                    }

                    messageInput.value = '';
                    attachmentInput.value = '';
                    uploadName.textContent = 'Images, PDF, DOC, DOCX up to 5MB';
                    messageInput.style.height = 'auto';
                    await sendTyping(false);
                    messagesHash = '';
                    window.clearTimeout(pollTimer);
                    fetchInbox();
                } catch (error) {
                    uploadName.textContent = 'Message failed to send. Please try again.';
                }
            });

            attachmentInput.addEventListener('change', () => {
                uploadName.textContent = attachmentInput.files[0]?.name ?? 'Images, PDF, DOC, DOCX up to 5MB';
            });

            messageInput.addEventListener('input', async () => {
                messageInput.style.height = 'auto';
                messageInput.style.height = `${Math.min(messageInput.scrollHeight, 160)}px`;
                await sendTyping(messageInput.value.trim().length > 0);

                window.clearTimeout(typingTimer);
                typingTimer = window.setTimeout(() => {
                    sendTyping(false);
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
                    headers: jsonHeaders,
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
                    window.location.reload();
                }
            });

            document.querySelectorAll('.auto-reply-edit').forEach((button) => {
                button.addEventListener('click', () => {
                    const card = button.closest('.auto-reply-card');
                    const reply = JSON.parse(card.dataset.reply);
                    document.getElementById('auto-reply-id').value = reply.id;
                    document.getElementById('auto-reply-trigger').value = reply.trigger_type;
                    document.getElementById('auto-reply-keyword').value = reply.keyword ?? '';
                    document.getElementById('auto-reply-text').value = reply.reply_text;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });

            document.querySelectorAll('.auto-reply-delete').forEach((button) => {
                button.addEventListener('click', async () => {
                    const response = await fetch(`${routes.sendBase}/auto-replies/${button.dataset.id}`, {
                        method: 'DELETE',
                        headers,
                    });

                    if (response.ok) {
                        window.location.reload();
                    }
                });
            });

            fetchInbox();
        })();
    </script>
@endpush
