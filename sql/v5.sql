CREATE TABLE mute (
    muter INT UNSIGNED NOT NULL,
    mutee INT UNSIGNED NOT NULL,
    PRIMARY KEY (muter, mutee),
    CONSTRAINT fk_mute_muter FOREIGN KEY (muter) REFERENCES users (id) on delete cascade,
    CONSTRAINT fk_mute_mutee FOREIGN KEY (mutee) REFERENCES users (id) on delete cascade
);
