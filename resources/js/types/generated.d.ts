export type Appointment = {
  uuid: string;
  employee: Employee;
  service: Service;
  date: string;
  starts_at: string;
  ends_at: string;
  canceled: boolean;
};
export type Availability = {
  date: string;
  slots: Array<Slot>;
};
export type DayOfWeek = 0 | 1 | 2 | 3 | 4 | 5 | 6;
export type Employee = {
  id: number;
  name: string;
  slug: string;
  avatar_url: string;
};
export type Service = {
  id: number;
  title: string;
  slug: string;
  duration: string;
  price: string;
};
export type Slot = {
  datetime: string;
  time: string;
  employees: { [key: number]: string };
};
