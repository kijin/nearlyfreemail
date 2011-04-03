
CREATE TABLE accounts
(
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    default_alias INTEGER NOT NULL REFERENCES aliases (id) DEFERRABLE INITIALLY DEFERRED,
    password      TEXT    NOT NULL,
    created_time  INTEGER NOT NULL,
    is_admin      INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX ix_accounts_created_time ON accounts (created_time);
CREATE INDEX ix_accounts_is_admin     ON accounts (is_admin);

CREATE TABLE aliases
(
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    account_id   INTEGER NOT NULL REFERENCES accounts (id) ON DELETE CASCADE,
    name         TEXT    NOT NULL,
    email        TEXT    NOT NULL UNIQUE,
    incoming_key TEXT    NOT NULL UNIQUE,
    created_time INTEGER NOT NULL
);
CREATE INDEX ix_aliases_account_id   ON aliases (account_id);
CREATE INDEX ix_aliases_name         ON aliases (name);
CREATE INDEX ix_aliases_email        ON aliases (email);
CREATE INDEX ix_aliases_incoming_key ON aliases (incoming_key);

CREATE TABLE settings
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    account_id INTEGER REFERENCES accounts (id) ON DELETE CASCADE,
    s_key      TEXT    NOT NULL,
    s_value    TEXT    NOT NULL
);
CREATE INDEX ix_settings_account_id ON settings (account_id);
CREATE INDEX ix_settings_s_key      ON settings (s_key);

CREATE TABLE filters
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    account_id INTEGER NOT NULL REFERENCES accounts (id) ON DELETE CASCADE,
    rule       TEXT    NOT NULL,
    action     TEXT    NOT NULL
);
CREATE INDEX ix_filters_account_id ON filters (account_id);

CREATE TABLE contacts
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    account_id INTEGER NOT NULL REFERENCES accounts (id) ON DELETE CASCADE,
    name       TEXT    NOT NULL,
    email      TEXT    NOT NULL,
    notes      TEXT    NOT NULL,
    last_used  INTEGER NOT NULL
);
CREATE INDEX ix_contacts_account_id ON contacts (account_id);
CREATE INDEX ix_contacts_name       ON contacts (name);
CREATE INDEX ix_contacts_email      ON contacts (email);
CREATE INDEX ix_contacts_notes      ON contacts (notes);
CREATE INDEX ix_contacts_last_used  ON contacts (last_used);

CREATE TABLE folders
(
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    account_id   INTEGER NOT NULL REFERENCES accounts (id) ON DELETE CASCADE,
    name         TEXT    NOT NULL,
    messages_all INTEGER NOT NULL DEFAULT 0,
    messages_new INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX ix_folders_account_id ON folders (account_id);
CREATE INDEX ix_folders_name       ON folders (name);

CREATE TABLE messages
(
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    account_id    INTEGER NOT NULL REFERENCES accounts (id) ON DELETE CASCADE,
    alias_id      INTEGER NOT NULL REFERENCES aliases (id) ON DELETE SET DEFAULT DEFAULT 0,
    folder_id     INTEGER NOT NULL REFERENCES folders (id) ON DELETE CASCADE,
    msgid         TEXT    NOT NULL,
    sender        TEXT    NOT NULL,
    recipient     TEXT    NOT NULL,
    refs          TEXT    NOT NULL,
    cc            TEXT    NOT NULL,
    bcc           TEXT    NOT NULL,
    reply_to      TEXT    NOT NULL,
    subject       TEXT    NOT NULL,
    content       TEXT    NOT NULL,
    charset       TEXT    NOT NULL,
    sent_time     INTEGER NOT NULL,
    received_time INTEGER NOT NULL,
    attachments   INTEGER NOT NULL,
    spam_score    REAL    NOT NULL,
    is_draft      INTEGER NOT NULL DEFAULT 0,  -- 0: received mail, 1: draft, 2: sent.
    is_read       INTEGER NOT NULL DEFAULT 0,  -- 0: unread, 1: read.
    is_replied    INTEGER NOT NULL DEFAULT 0,  -- 0: none, 1: replied, 2: forwarded, 3: replied and forwarded.
    is_starred    INTEGER NOT NULL DEFAULT 0,  -- 0: none, 1: starred.
    notes         TEXT    NOT NULL
);
CREATE INDEX ix_messages_account_id    ON messages (account_id);
CREATE INDEX ix_messages_alias_id      ON messages (alias_id);
CREATE INDEX ix_messages_folder_id     ON messages (folder_id);
CREATE INDEX ix_messages_msgid         ON messages (msgid);
CREATE INDEX ix_messages_sent_time     ON messages (sent_time);
CREATE INDEX ix_messages_received_time ON messages (received_time);
CREATE INDEX ix_messages_attachments   ON messages (attachments);
CREATE INDEX ix_messages_is_draft      ON messages (is_draft);
CREATE INDEX ix_messages_is_read       ON messages (is_read);
CREATE INDEX ix_messages_is_replied    ON messages (is_replied);
CREATE INDEX ix_messages_is_starred    ON messages (is_starred);

CREATE TABLE attachments
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    message_id INTEGER NOT NULL REFERENCES messages (id) ON DELETE CASCADE,
    filename   TEXT    NOT NULL,
    filesize   INTEGER NOT NULL,
    deleted    INTEGER NOT NULL,
    content    BLOB    NOT NULL
);
CREATE INDEX ix_attachments_message_id ON attachments (message_id);

CREATE TABLE originals
(
    message_id INTEGER NOT NULL REFERENCES messages (id) ON DELETE CASCADE,
    subject    BLOB    NOT NULL,
    content    BLOB    NOT NULL,
    source     BLOB    NOT NULL
);
CREATE INDEX ix_originals_message_id ON originals (message_id);
