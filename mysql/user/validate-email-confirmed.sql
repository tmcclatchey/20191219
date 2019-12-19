SELECT
    COUNT(`qaId`) AS `count`
FROM
    `user_qa`
WHERE
    `userEmailConfirmed` > 0 AND
    `userId` = :userId