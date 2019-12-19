SELECT
    `userId`,
    `userLogin`,
    `userName`,
    `userHash`,
    `userHashUpdated`,
    `userEmail`,
    `userEmailUpdated`,
    `userEmailConfirmed`,
    `userMobile`,
    `userMobileUpdated`,
    `userMobileFonfirmed`,
    `roleId`,
    `managerId`
FROM 
    `user`
WHERE
    (
        `managerId`=:managerId
    )
    AND
    (
        (`userLogin` LIKE CONCAT('%', :query, '%')) OR
        (`userName` LIKE CONCAT('%', :query, '%')) OR
        (`userEmail` LIKE CONCAT('%', :query, '%')) OR
        (`userMobile` LIKE CONCAT('%', :query, '%')) OR
    )
LIMIT
    :startingIndex, :recordLimit