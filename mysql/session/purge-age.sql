DELETE FROM
    `session`
WHERE
    `sessionActivity` < :maximumAge