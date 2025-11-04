export interface Student {
  id: number;
  id_number: string;
  last_name: string;
  first_name: string;
  middle_name: string;
  birth_day: string;
  course: string;
  gender: string;
}

export interface EnrolledStudent {
  id: number;
  last_name: string;
  first_name: string;
  middle_name: string | null;
  id_number: string;
  course: string;
  birth_day: string;
  gender: string;
  created_at: string;
}

export interface Batch {
  id: number;
  name: string;
  description: string | null;
  access_key: number;
  isLocked: boolean;
  created_at: Date;
}

export interface School {
  id: number;
  name: string;
}

export interface Result {
  student: Student;
  batch: Batch;
  id: number;

  total_score: number;
  sai_t: number;
  pba_pr_t: number;
  pba_s_t: number;
  pbg_pr_t: number;
  pbg_s_t: number;
  verbal: number;
  non_verbal: number;

  verbal_comprehension: number;
  verbal_comprehension_category: string;

  verbal_reasoning: number;
  verbal_reasoning_category: string;

  quantitative_reasoning: number;
  quantitative_reasoning_category: string;

  figural_reasoning: number;
  figural_reasoning_category: string;

  total_performance_category: string;
  verbal_performance_category: string;
  non_verbal_performance_category: string;

  created_at: string;
}

interface UploadableField {
  file: File | null;
  preview: string | null;
}

export interface Question {
  id: null;
  test_type: string;

  // Question
  question: string;
  question_image: UploadableField | null; // image format

  // Choices
  ch1: string;
  ch1_image: UploadableField | null;

  ch2: string;
  ch2_image: UploadableField | null;

  ch3: string;
  ch3_image: UploadableField | null;

  ch4: string;
  ch4_image: UploadableField | null;

  ch5: string;
  ch5_image: UploadableField | null;

  answer: 'ch1' | 'ch2' | 'ch3' | 'ch4' | 'ch5';

  qtype: string;
  format: 'text' | 'photo';
}

export interface Recent {
  id: number;
  student_id: number;
  batch_id: number;
  total_score: number;
  created_at: string;
  batch: Batch;
  student: Student;
}

export interface Count {
  batch: number;
  enrolledstudents: number;
  finishedexams: number;
  recent: Recent[];
}

export interface SchoolSettingsForm {
  school_name: string;
  school_logo: File | null;
}

interface User {
  id: number;
  name: string;
  email: string;
}
