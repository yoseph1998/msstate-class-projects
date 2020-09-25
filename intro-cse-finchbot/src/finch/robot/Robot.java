package finch.robot;

import edu.cmu.ri.createlab.terk.robot.finch.Finch;
import finch.robot.framework.Scheduler;
import finch.robot.gui.RobotWindow;
import finch.robot.subsystems.DriveTrain;
import finch.robot.subsystems.LED;

/**
 * The class where the high level code begins.
 */
public class Robot {
    private static Robot robot;
    private static Scheduler scheduler = Scheduler.getInstance();

    private Finch finch = null;
    private DriveTrain driveTrain = null;
    private LED led = null;
    private RobotDriver driver = null;
    private RobotWindow window;

    private Robot() {

        finch = new Finch();
        driveTrain = new DriveTrain();
        led = new LED();
        driver = new RobotDriver();
        window = new RobotWindow();
    }

    /**
     * If an instance does not exits it will create a new one.
     * This method should never be called in any constructor except the Scheduler's constructor, or a program infinite
     * could occur freezing the program.
     *
     * @return An instance of the Robot.
     */
    public static Robot getInstance() {
        if (robot == null)
            robot = new Robot();
        return robot;
    }

    /**
     * Will run tests if the arguments call to do so.
     */
    public void init() {
        if (driver != null)
            driver.init();
        if (driveTrain != null)
            driveTrain.init();
    }

    /**
     * The robot's iterative method.
     * Called every loop.
     */
    public void update() {
        if (driver != null)
            driver.update();
    }

    /**
     * The last method that is called in the program. Before the robot stops.
     */
    public void end() {
        if (driver != null)
            driver.end();
        if (driveTrain != null)
            driveTrain.end();
        if (window != null)
            window.stop();
        if (finch != null)
            finch.quit();
    }

    public Finch getFinch() {
        return finch;
    }

    public DriveTrain getDriveTrain() {
        return driveTrain;
    }

    public RobotWindow getWindow() {
        return window;
    }

    public LED getLed() {
        return led;
    }
}

