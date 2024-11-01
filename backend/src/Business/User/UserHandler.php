<?php

namespace App\Business\User;

use App\Entity\Department;
use App\Entity\Mitarbeiter;
use App\Entity\MitarbeiterToDepartment;
use App\Entity\User;
use App\Repository\DepartmentRepository;
use App\Repository\MitarbeiterRepository;
use App\Repository\MitarbeiterToDepartmentRepository;
use App\Repository\UserRepository;

class UserHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly DepartmentRepository $departmentRepository,
        private readonly MitarbeiterRepository $mitarbeiterRepository,
        private readonly MitarbeiterToDepartmentRepository $mitarbeiterToDepartmentRepository
    ) {
    }

    public function saveUser(array $data): User
    {
        $user = $this->userRepository->find($data['id']);
        $departments = [];

        if (!$user) {
            $user = new User();
        }

        $user->setFirstname(trim($data['firstname']));
        $user->setLastname(trim($data['lastname']));
        $user->setMitarbeiterId((int)$data['mitarbeiterId']);
        $user->setUpdatedAt(new \DateTime());

        if ($user->getCreatedAt() === null) {
            $user->setCreatedAt(new \DateTime());
        }
        $this->userRepository->save($user);

        $mitarbeiter = $this->mitarbeiterRepository->findOneBy(['user' => $user]);

        if (!empty($data['departments'])) {
            $departments = $this->departmentRepository->findBy(['id' => $data['departments']]);
        }

        if ($mitarbeiter === null) {
            $mitarbeiter = new Mitarbeiter();

            $mitarbeiter->setVorname($data['firstname']);
            $mitarbeiter->setNachname($data['lastname']);

            $mitarbeiter->setUser($user);
        }

        if (!empty($departments)) {
            $existingDepartment = $mitarbeiter->getMitarbeiterToDepartments()->map(function (MitarbeiterToDepartment $mitarbeiterToDepartment) {
                return $mitarbeiterToDepartment->getDepartment();
            });
            $existingDepartmentIdMap = [];
            $requestDepartmentIdMap = [];

            foreach ($existingDepartment as $department) {
                $existingDepartmentIdMap[$department->getId()] = $department->getId();
            }

            /** @var Department $department */
            foreach ($departments as $department) {
                $requestDepartmentIdMap[$department->getId()] = $department->getId();

                if (isset($existingDepartmentIdMap[$department->getId()])) {
                    continue;
                }
                $mitarbeiterToDepartment = new MitarbeiterToDepartment();
                $mitarbeiterToDepartment->setDepartment($department);
                $mitarbeiterToDepartment->setMitarbeiter($mitarbeiter);

                $mitarbeiter->addMitarbeiterToDepartment($mitarbeiterToDepartment);
            }

            if (!empty($existingDepartmentIdMap)) {
                $removeDepartmentIds = array_diff($existingDepartmentIdMap, $requestDepartmentIdMap);
                foreach ($removeDepartmentIds as $removeDepartmentId) {
                    $mitarbeiterToDepartment = $mitarbeiter->getMitarbeiterToDepartments()->filter(
                        function (MitarbeiterToDepartment $mitarbeiterToDepartment) use ($removeDepartmentId) {
                            return $mitarbeiterToDepartment->getDepartment()->getId() === $removeDepartmentId;
                        }
                    )->first();
                    $this->mitarbeiterToDepartmentRepository->remove($mitarbeiterToDepartment);
                }
            }
        }

        $this->mitarbeiterRepository->save($mitarbeiter);

        return $user;
    }

    public function deleteUser($userId): void
    {
        /** @var User $user */
        $user = $this->userRepository->find($userId);

        $mitarbeiter = $user->getMitarbeiter();

        $mitarbeiter->setUser(null);
        $this->mitarbeiterRepository->remove($mitarbeiter);
        $this->userRepository->remove($user);
    }
}