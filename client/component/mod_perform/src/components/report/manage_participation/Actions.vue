<!--
  This file is part of Totara Enterprise Extensions.

  Copyright (C) 2020 onwards Totara Learning Solutions LTD

  Totara Enterprise Extensions is provided only to Totara
  Learning Solutions LTD's customers and partners, pursuant to
  the terms and conditions of a separate agreement with Totara
  Learning Solutions LTD or its affiliate.

  If you do not have an agreement with Totara Learning Solutions
  LTD, you may not access, use, modify, or distribute this software.
  Please contact [licensing@totaralearning.com] for more information.

  @author Samantha Jayasinghe <samantha.jayasinghe@totaralearning.com>
  @module totara_perform
-->

<template>
  <div>
    <ParticipantDeleteActionModal
      v-if="isParticipantReport"
      :participant-instance-id="id"
      :report-type="reportType"
      :delete-modal-open="deleteModalOpen"
      @modal-close="closeDeleteModal"
    />
    <SubjectDeleteActionModal
      v-if="isSubjectReport"
      :subject-instance-id="id"
      :report-type="reportType"
      :delete-modal-open="deleteModalOpen"
      @modal-close="closeDeleteModal"
    />
    <SubjectOpenCloseActionModal
      v-if="isSubjectReport"
      :modal-open="showModalOpen"
      :subject-instance-id="id"
      :is-open="isOpen"
      :report-type="reportType"
      @modal-close="modalClose"
    />
    <ParticipantOpenCloseActionModal
      v-if="isParticipantReport"
      :modal-open="showModalOpen"
      :participant-instance-id="id"
      :is-open="isOpen"
      :report-type="reportType"
      @modal-close="modalClose"
    />
    <SectionOpenCloseActionModal
      v-if="isSectionReport"
      :modal-open="showModalOpen"
      :participant-section-id="id"
      :is-open="isOpen"
      :report-type="reportType"
      @modal-close="modalClose"
    />
    <a
      v-if="isSubjectReport && canAddParticipants && !isParticipantPending"
      :href="participationManagementUrl"
      :title="$str('activity_participants_add', 'mod_perform')"
    >
      <ParticipantAddIcon />
    </a>
    <template v-if="showActions && !isParticipantPending">
      <ButtonIcon
        v-if="isOpen"
        :aria-label="$str('button_close', 'mod_perform')"
        :styleclass="{ transparentNoPadding: true }"
        @click="showModal()"
      >
        <LockIcon />
      </ButtonIcon>
      <ButtonIcon
        v-else
        :aria-label="$str('button_reopen', 'mod_perform')"
        :styleclass="{ transparentNoPadding: true }"
        @click="showModal()"
      >
        <UnlockIcon />
      </ButtonIcon>
    </template>
    <template v-if="showActions">
      <ButtonIcon
        v-if="isSubjectReport || isParticipantReport"
        :aria-label="$str('button_delete', 'mod_perform')"
        :styleclass="{ transparentNoPadding: true }"
        @click="showDeleteModal()"
      >
        <DeleteIcon />
      </ButtonIcon>
    </template>
  </div>
</template>
<script>
import ButtonIcon from 'tui/components/buttons/ButtonIcon';
import LockIcon from 'tui/components/icons/Lock';
import ParticipantDeleteActionModal from 'mod_perform/components/report/manage_participation/ParticipantDeleteActionModal';
import ParticipantOpenCloseActionModal from 'mod_perform/components/report/manage_participation/ParticipantOpenCloseActionModal';
import SectionOpenCloseActionModal from 'mod_perform/components/report/manage_participation/SectionOpenCloseActionModal';
import SubjectOpenCloseActionModal from 'mod_perform/components/report/manage_participation/SubjectInstanceOpenCloseActionModal';
import SubjectDeleteActionModal from 'mod_perform/components/report/manage_participation/SubjectInstanceDeleteActionModal';
import ParticipantAddIcon from 'tui/components/icons/AddUser';
import UnlockIcon from 'tui/components/icons/Unlock';
import DeleteIcon from 'tui/components/icons/Delete';

const REPORT_TYPE_SUBJECT_INSTANCE = 'SUBJECT_INSTANCE';
const REPORT_TYPE_PARTICIPANT_INSTANCE = 'PARTICIPANT_INSTANCE';
const REPORT_TYPE_PARTICIPANT_SECTION = 'PARTICIPANT_SECTION';

export default {
  components: {
    ButtonIcon,
    LockIcon,
    ParticipantDeleteActionModal,
    ParticipantOpenCloseActionModal,
    ParticipantAddIcon,
    SectionOpenCloseActionModal,
    SubjectOpenCloseActionModal,
    SubjectDeleteActionModal,
    UnlockIcon,
    DeleteIcon,
  },
  props: {
    reportType: {
      type: String,
    },
    id: {
      type: String,
    },
    isOpen: {
      type: Boolean,
    },
    showActions: {
      type: Boolean,
      required: false,
      default: true,
    },
    canAddParticipants: {
      type: Boolean,
    },
    isParticipantPending: {
      type: Boolean,
    },
  },
  data() {
    return {
      showModalOpen: false,
      deleteModalOpen: false,
    };
  },
  computed: {
    isSectionReport() {
      return this.reportType === REPORT_TYPE_PARTICIPANT_SECTION;
    },
    isParticipantReport() {
      return this.reportType === REPORT_TYPE_PARTICIPANT_INSTANCE;
    },
    isSubjectReport() {
      return this.reportType === REPORT_TYPE_SUBJECT_INSTANCE;
    },
    /**
     * Get the url to the participation management
     *
     * @return {string}
     */
    participationManagementUrl() {
      return this.$url(
        '/mod/perform/manage/participation/add_participants.php',
        {
          subject_instance_id: this.id,
        }
      );
    },
  },
  methods: {
    modalClose() {
      this.showModalOpen = false;
    },
    showModal() {
      this.showModalOpen = true;
    },
    showDeleteModal() {
      this.deleteModalOpen = true;
    },
    closeDeleteModal() {
      this.deleteModalOpen = false;
    },
  },
};
</script>
<lang-strings>
  {
  "mod_perform": [
    "activity_participants_add",
    "button_reopen",
    "button_close",
    "button_delete"
  ]
  }
</lang-strings>
