SELECT
    COUNT(`qaId`) AS `count`
FROM
    `user_qa`
WHERE
    `userMobileConfirmed` > 0 AND
    `userId` = :userId